<?php

namespace LaravelWechatpayV3\Service\Merchant\Media;

use LaravelWechatpayV3\Kernel\BaseClient;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public static function className()
    {
        return 'merchant/media';
    }

    /**
     * @param string $name
     * @param StreamInterface $content
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function upload(UploadedFile $file, array $options = [])
    {
        $fileName = $file->getClientOriginalName();
        $splFile = $file->openFile('r');
        $content = $splFile->fread($splFile->getSize());
        $signPayload = json_encode([
            'filename' => $fileName,
            'sha256' => hash('sha256', $content),
        ]);

        $multipart = [
            [
                'name' => 'meta',
                'contents' => $signPayload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
            [
                'name' => 'file',
                'filename' => $fileName,
                'contents' => $content,
                'headers' => [
                    'Content-Type' => $file->getMimeType(),
                ],
            ],
        ];

        $url = self::classUrl().'/upload';
        $opts = $options + ['multipart' => $multipart, 'sign_payload' => $signPayload];

        return $this->request('POST', $url, $opts);
    }

}
