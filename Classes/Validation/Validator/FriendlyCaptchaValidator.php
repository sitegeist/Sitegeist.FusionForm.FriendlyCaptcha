<?php
declare(strict_types=1);

namespace Sitegeist\FusionForm\FriendlyCaptcha\Validation\Validator;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Annotations AS Flow;
use Neos\Flow\Validation\Validator\AbstractValidator;

class FriendlyCaptchaValidator extends AbstractValidator
{
    /**
     * @Flow\InjectConfiguration(path="siteSecret")
     * @var string
     */
    protected $siteSecret;

    /**
     * @Flow\InjectConfiguration(path="siteKey")
     * @var string
     */
    protected $siteKey;

    /**
     * @Flow\Inject
     * @var Bootstrap
     */
    protected $bootstrap;

    protected $supportedOptions = [
        'siteKey' => [null, 'siteKey', 'string', false],
        'siteSecret' => [null, 'siteSecret', 'string', false]
    ];


    protected function isValid($captcha): void
    {
        $siteKey = $this->options['siteKey'] ?: $this->siteKey;
        $siteSecret = $this->options['siteSecret'] ?: $this->siteSecret;
        $captchaResponse = $captcha ?? false;
        if ($captchaResponse) {
            /** @phpstan-ignore-next-line */
            $client = new CurlEngine();
            $client->setOption(CURLOPT_RETURNTRANSFER, true );
            $response = $client->sendRequest(
                new ServerRequest(
                    'POST',
                    new Uri('https://api.friendlycaptcha.com/api/v1/siteverify'),
                    [
                        'Content-Type' => 'application/json',
                    ],
                    json_encode([
                        'secret' => $siteSecret,
                        'solution' => $captchaResponse,
                        'siteKey' => $siteKey
                    ])
                )
            );
            if (!json_decode($response->getBody()->getContents() ?: '')->success) {
                $this->addError('Captcha is invalid.', 20230123115302);
            }
            return;
        }
        $this->addError('Der Request konnte nicht gelesen werden.', 1649869170);
    }

}
