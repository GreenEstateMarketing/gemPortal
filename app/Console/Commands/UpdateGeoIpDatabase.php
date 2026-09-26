<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PharData;
use Throwable;

class UpdateGeoIpDatabase extends Command
{
    /**
     * @var string
     */
    protected $signature = 'geoip:update-db';

    /**
     * @var string
     */
    protected $description = 'Download the latest MaxMind GeoLite2 City database used for IP-based visitor location';

    public function handle()
    {
        $licenseKey = setting('maxmind_license_key');

        if (! $licenseKey) {
            $this->error('MaxMind license key is not configured. Set it in Admin > Real Estate > Settings.');

            return 1;
        }

        $tmpDir = storage_path('app/geoip/tmp_' . Str::random(8));
        File::ensureDirectoryExists($tmpDir);

        try {
            $this->info('Downloading GeoLite2-City database...');

            $url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=' . $licenseKey . '&suffix=tar.gz';
            $response = Http::timeout(120)->get($url);

            if (! $response->successful()) {
                $this->error('Download failed: HTTP ' . $response->status());

                return 1;
            }

            $archive = $tmpDir . '/GeoLite2-City.tar.gz';
            File::put($archive, $response->body());

            $phar = new PharData($archive);
            $phar->decompress();
            (new PharData(Str::beforeLast($archive, '.gz')))->extractTo($tmpDir);

            $mmdb = collect(File::allFiles($tmpDir))
                ->first(function ($file) {
                    return $file->getExtension() === 'mmdb';
                });

            if (! $mmdb) {
                $this->error('No .mmdb file found in the downloaded archive.');

                return 1;
            }

            $target = storage_path('app/geoip/GeoLite2-City.mmdb');
            File::ensureDirectoryExists(dirname($target));
            File::copy($mmdb->getPathname(), $target . '.new');
            File::move($target . '.new', $target);

            $this->info('GeoLite2 database updated: ' . $target);
        } catch (Throwable $e) {
            $this->error('GeoIP database update failed: ' . $e->getMessage());

            return 1;
        } finally {
            File::deleteDirectory($tmpDir);
        }

        return 0;
    }
}
