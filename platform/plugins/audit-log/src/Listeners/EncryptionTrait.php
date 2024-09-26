<?php

namespace Botble\AuditLog\Listeners;
use Exception;
use Cache;

trait EncryptionTrait
{
    public function encryptWithPublicKey($data): string
    {
        $publicKey = openssl_pkey_get_public(env('RSA_PUBLIC_KEY'));

        if (!$publicKey) {
            throw new Exception("Unable to load public key");
        }

        openssl_public_encrypt($data, $encrypted, $publicKey);
        return base64_encode($encrypted);
    }

    public function decryptWithPrivateKey($encryptedData): ?string
    {
        // $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDU/Mnu6C+NwZUV\nfgmqUmjLzBiUQzgBDutCn6YZKGlPaFDJX+A//MMLFI3KgqFIL95MGIwjq3gPh9RH\n203GWbwavzpJvrrrkjRYTqFvHu/E6bXml/3VjKopL4qvsFqq1ipYQGSldUZhx0wM\nTJyOfzM+5u8NzXKY0HYoKHbGwlzO6x61+IA7jjhQn98zUmdwbBPApXhRUlrMXRA0\n1l84xH+k/EB59W3uaT9CHhUcoodFrLQPzRXlnMbo/ipPULHT7heQ7HfmzHVq0Tn5\nhZ4N3SzAb8eALlkVU5smsPi3gGr0HGUQ09+Akbmrorzd8vcOCnWqGMwS8XKjz/cv\nC5LTSeoPAgMBAAECggEAJ4OAeSS1UdDEtliMNX+Vdp22P6da2ANrDRCuUYOIShWZ\ny0pQwb5EdyVIivYCMvDCho6VTTbODt+NuAkNHEvglHu+thi+995HfMyVsZZlODx9\npTq0em5e0UZDLgYDRCd4cqf7pCCmPpSpXKzH7L3XhYd1eTOQ253tFdUv9/7uFer9\nQj5AQo10Jr0VTiACvEW19abdkSvr4A/p216ecAQJWVRB5fWJJshWtxUwDNmQ5hlP\nFMDQIIjIt0xdmQFlRD7ZCxAc3e+E5imR5LZWyiM1ZK2LMjWfgGoFNpA3E2dUkOuu\nRgHFAvQQ1x8Hqo2D/ZkDYhIfCMgfP3Ckx07yHdlYRQKBgQDrMBntmCmXtANXi8En\n9giC+0HDxpFg/j6nevaHMOmuHxTv0HBBQXJ/ttQGsdrjaLlJjuWuvgFfI6vsb/5e\ntmZYLpAec8rJlM6LYTQf7b0X3kSZ/KtunQLnP+ssjZDl776vIflmzTv9wLytbW/V\nipfzPgn3WmdvU2e6s5K3A4KjtQKBgQDn1cNmpcHdvdeZc86VtC0XO44bGetJCUyI\nQsanxO5nFMg45ZTmg05pZfXdOzawzv9UWiS6lCi+a/4jhkITKDlSQcDhCzz8v1Wr\nhohEj5zgEZ4x0yABJ/00nu8IABG8J8lJzMj+yTsPuVZflaD3Gxj2RCXMy6zOW9/A\nbQi1pJA5MwKBgQDiN2EpHKwdBAQW6BEBW/Bx9DUcl1lsfwBK3cZU4OJUHgdoaAgh\nbE8ysuucCOSuyiM2sqEQBCiTl18dy9dSyIUGmrr634uVe3FztSqK74RredpodxV6\nDsIlJmEReJV/5at3DumyTQRAHmwdMF9aebWQPQMfDbDh7sqeVW1wZYr55QKBgF98\nowO3R/c9xvxUP4VXda74/5nX/hnR86y33DyjlxHr9F/C56Zd9MDilvas+eSvDWk/\ny5rxhSqRLlaRaMudKKbhoEDQsSjk4bNJMP0ULaf4ebDJ5Ye0Ycz3nTotVSCrPnPg\nHfUbCvF6A8JQzcCZb5mXDf6g8Sb5nloSTqEKC8ETAoGASAZKEZw5jfAgkwu5ab+6\nLrBx24UHz/7LxfTeV1nBkRAQKpXRwXaSpBhzaS5dBPZN7QVuchpxKswWccE0DDmW\noV6ng25d6bk5Y/zcORMbzLtYNglSJ3BE+MDYcgeXOi8u7ivhmMtcqcRYag1S0zhq\n1c4Fn/zl+Q7atllBMILf+0Y=\n-----END PRIVATE KEY-----";
        $privateKey = Cache::get('decryption_key');
        $privateKey = openssl_pkey_get_private($privateKey);

        if (!$privateKey) {
            throw new Exception("Unable to load private key. Please provide correct private key.");
        }

        $encryptedData = base64_decode($encryptedData);
        openssl_private_decrypt($encryptedData, $decrypted, $privateKey);

        return $decrypted;
    }
}