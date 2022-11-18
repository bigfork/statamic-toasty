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
        Cache::put('toasty.invalid', false);

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

                while(\count($avilableUrls) > 0) {
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
                                $parsedUrl = parse_url($absoluteUrl);
                                $path = $parsedUrl['path'] ?? null;

                                // TODO: Interpret the query parameters of the found url to allow query parameter whitelisting
                                if ($path && !isset($foundUrls[$path])) {
                                    $foundUrls[$path] = true;
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
}
