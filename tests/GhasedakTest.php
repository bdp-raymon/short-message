<?php


namespace Alish\ShortMessage\Tests;

use Alish\ShortMessage\Facade\ShortMessage;
use Alish\ShortMessage\Messages\GhasedakOtp;
use Alish\ShortMessage\SentSuccessful;
use Alish\ShortMessage\ShortMessageServiceProvider;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class GhasedakTest extends TestCase
{

    protected $apiKey = 'ghasedak-api-key';

    protected function getPackageProviders($app)
    {
        return [ShortMessageServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('short-message.ghasedak.api-key', 'ghasedak-api-key');
    }

    /**
     * @test
     */
    public function success_send_simple_message()
    {
        Http::fake(function (\Illuminate\Http\Client\Request $request) {

            $this->assertEquals($request->url(), 'https://api.ghasedak.io/v2/sms/send/pair');

            $this->assertArrayHasKey('message', $request->data());
            $this->assertArrayHasKey('receptor', $request->data());
            $this->assertEquals($this->apiKey, $request->header('apikey')[0]);

            return Http::response($this->successResponsePayload($request->data()['receptor']), 200);
        });

        $response = ShortMessage::driver('ghasedak')->send(['09304900220'], 'text');

        $this->assertInstanceOf(SentSuccessful::class, $response);
    }

    /**
     * @test
     */
    public function sending_simple_message_accept_more_options()
    {

        $sendDate = 132456789;
        $checkId = [1];
        $lineNumber = 132456789;

        Http::fake(function (\Illuminate\Http\Client\Request $request) use($sendDate, $checkId, $lineNumber) {

            $this->assertArrayHasKey('senddate', $request->data());
            $this->assertEquals($sendDate, $request->data()['senddate']);
            $this->assertArrayHasKey('checkid', $request->data());
            $this->assertEquals(implode(',', $checkId), $request->data()['checkid']);
            $this->assertArrayHasKey('linenumber', $request->data());
            $this->assertEquals($lineNumber, $request->data()['linenumber']);

            return Http::response($this->successResponsePayload(), 200);
        });

        ShortMessage::driver('ghasedak')
            ->sendDate($sendDate)
            ->checkId($checkId)
            ->lineNumber($lineNumber)
            ->send(['09304900220'], 'text');
    }

    /**
     * @test
     */
    public function send_otp_message()
    {

        $receptor = ['09304900220'];
        $template = '123';
        $params = [1, 2];

        Http::fake(function (\Illuminate\Http\Client\Request $request) use ($receptor, $template, $params) {

            $this->assertEquals($request->url(), 'https://api.ghasedak.io/v2/verification/send/simple');

            $this->assertArrayHasKey('receptor', $request->data());
            $this->assertEquals(implode(',', $receptor), $request->data()['receptor']);
            $this->assertArrayHasKey('type', $request->data());
            $this->assertEquals(1, $request->data()['type']);
            $this->assertArrayHasKey('template', $request->data());
            $this->assertEquals($template, $request->data()['template']);

            foreach ($params as $index => $param) {
                $this->assertArrayHasKey('param' . ($index + 1), $request->data());
                $this->assertEquals($params[$index], $request->data()['param' . ($index + 1)]);
            }

            $this->assertEquals($this->apiKey, $request->header('apikey')[0]);

            return Http::response($this->successResponsePayload($request->data()['receptor']), 200);
        });

        $response = ShortMessage::driver('ghasedak')->otp($receptor, GhasedakOtp::template($template)->params($params));

        $this->assertInstanceOf(SentSuccessful::class, $response);
    }

    protected function successResponsePayload($items = [])
    {
        return [
            "result" => [
                "code" => 200,
                "message" => "success"
            ],
            "items" => $items
        ];
    }

}
