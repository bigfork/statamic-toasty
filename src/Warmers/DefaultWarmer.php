<?php

namespace WithCandour\StatamicToasty\Warmers;

use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Site;
use Statamic\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use WithCandour\StatamicToasty\Exceptions\WarmerException;

class DefaultWarmer extends Warmer
{
    /**
     * @inheritDoc
     */
    public function warm(mixed $sites = 'all', bool $output = false): void
    {
        $cacheKey = Str::random();
        Cache::put('toasty.invalid', false);
        Cache::put('toasty.key', $cacheKey);

        if ($sites === 'all') {
            $sites = \config('statamic.toasty.sites', []);
        }

        $homepages = [];

        if (\is_array($sites)) {
            $homepages = \collect($sites)
                ->mapWithKeys(function ($site) {
                    $statamicSite = Site::get($site);
                    if ($statamicSite) {
                        return [
                            $statamicSite->handle() => $this->makeUrl($statamicSite, $statamicSite->absoluteUrl())
                        ];
                    }

                    return null;
                })
                ->filter()
                ->all();
        } else {
            $statamicSite = Site::get($sites);
            if ($statamicSite) {
                $homepages[$statamicSite->handle] = $this->makeUrl($statamicSite, $statamicSite->absoluteUrl());
            }
        }

        try {
            // Create an http client for crawling
            $httpClient = HttpClient::create([
                'headers' => [
                    'User-Agent' => 'StatamicToasty',
                ],
                'timeout' => 60,
            ]);

            $client = new Client($httpClient);

            // Loop over our sites and get the urls for each
            foreach ($homepages as $site => $url) {

                $avilableUrls = [$url];
                $foundUrls = ['/' => true];

                while(\count($avilableUrls) > 0 && Cache::get('toasty.key', null) === $cacheKey) {
                    $url = \array_pop($avilableUrls);

                    if ($output) {
                        $this->output("[{$url->site()->handle()}] Caching: {$url->url()}; Available: " . \count($avilableUrls));
                    }

                    $site = $url->site();
                    $crawler = $client->request('GET', $url->url());

                    // Get all anchor links on the page
                    $crawler->filter('a')->each(function (Crawler $node) use (&$avilableUrls, &$foundUrls, $site) {
                        $foundUrl = $node->link()->getUri();

                        // Ensure we've not found a mailto/tel link
                        if (!empty($foundUrl) && !Str::startsWith($foundUrl, ['mailto:', 'tel:'])) {

                            // Ensure we have an absolute url
                            if (Str::startsWith($foundUrl, ['/', '#'])) {
                                $absoluteUrl = Str::ensureLeft($foundUrl, Str::ensureRight($site->absoluteUrl(), '/'));
                            } else {
                                $absoluteUrl = $foundUrl;
                            }

                            // Ensure we're working with an internal link for this site
                            if ($this->shouldCrawl($absoluteUrl, $site)) {
                                $parsedFoundUrl = $this->parseFoundUrl($absoluteUrl);

                                if ($parsedFoundUrl && !isset($foundUrls[$parsedFoundUrl])) {
                                    $foundUrls[$parsedFoundUrl] = true;
                                    $avilableUrls[] = $this->makeUrl($site, $foundUrl);
                                }
                            }
                        }
                    });
                }
            }
        } catch(\Exception $e) {
            throw new WarmerException($e->getMessage());
        }
    }

    /**
     * Get a value to add to the $foundUrls array which tracks which pages have been crawled.
     * This value should include the crawlable query parameters to allow pages to be crawled
     * multiple times if applicable.
     *
     * @param string $original
     * @return string|null
     */
    protected function parseFoundUrl(string $original): ?string
    {
        $crawlableParams = \config('statamic.toasty.carwlable_query_parameters', []);
        $parsedUrl = \parse_url($original);
        $url = $parsedUrl['path'] ?? null;

        if (!$url) {
            return null;
        }

        $query = $parsedUrl['query'] ?? null;

        if (!$query) {
            return $url;
        }

        $params = \explode('&', $query);

        $paramsToAdd = \collect($params)
            ->filter(function ($param) use ($crawlableParams) {
                $name = \explode('=', $param)[0] ?? null;

                if (!$name) {
                    return false;
                }

                return \in_array($name, $crawlableParams);
            })
            ->sort(function ($param) {
                return \explode('=', $param)[0] ?? null;
            });

        if ($paramsToAdd->count() < 1) {
            return $url;
        }

        $paramsToAdd = $paramsToAdd->join('&');

        return "{$url}?{$paramsToAdd}";
    }
}
