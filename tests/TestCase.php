<?php

namespace Flat3\OData\Tests;

use Flat3\OData\Exception\Protocol\BadRequestException;
use Flat3\OData\Exception\Protocol\InternalServerErrorException;
use Flat3\OData\Exception\Protocol\MethodNotAllowedException;
use Flat3\OData\Exception\Protocol\NoContentException;
use Flat3\OData\Exception\Protocol\NotAcceptableException;
use Flat3\OData\Exception\Protocol\NotFoundException;
use Flat3\OData\Exception\Protocol\NotImplementedException;
use Flat3\OData\Exception\Protocol\PreconditionFailedException;
use Flat3\OData\Exception\Protocol\ProtocolException;
use Flat3\OData\ServiceProvider;
use Flat3\OData\Tests\Data\TestModels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Spatie\Snapshots\MatchesSnapshots;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use MatchesSnapshots;
    use RefreshDatabase;
    use TestModels;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }

    protected function assertExceptionSnapshot(Request $request, string $exceptionClass)
    {
        try {
            $this->req($request);
            throw new RuntimeException('Failed to throw exception');
        } catch (ProtocolException $e) {
            if (!$e instanceof $exceptionClass) {
                throw new RuntimeException('Incorrect exception thrown: ' . get_class($e));
            }

            $this->assertMatchesObjectSnapshot($e->serialize());
        }
    }

    protected function assertNotAcceptable(Request $request)
    {
        $this->assertExceptionSnapshot($request, NotAcceptableException::class);
    }

    protected function assertMethodNotAllowed(Request $request)
    {
        $this->assertExceptionSnapshot($request, MethodNotAllowedException::class);
    }

    protected function assertNotFound(Request $request)
    {
        $this->assertExceptionSnapshot($request, NotFoundException::class);
    }

    protected function assertNoContent(Request $request)
    {
        $this->assertExceptionSnapshot($request, NoContentException::class);
    }

    protected function assertPreconditionFailed(Request $request)
    {
        $this->assertExceptionSnapshot($request, PreconditionFailedException::class);
    }

    protected function assertNotImplemented(Request $request)
    {
        $this->assertExceptionSnapshot($request, NotImplementedException::class);
    }

    protected function assertInternalServerError(Request $request)
    {
        $this->assertExceptionSnapshot($request, InternalServerErrorException::class);
    }

    protected function assertBadRequest(Request $request)
    {
        $this->assertExceptionSnapshot($request, BadRequestException::class);
    }

    public function urlToReq(string $url): Request
    {
        $request = Request::factory();

        $url = parse_url($url);
        $request->path($url['path'], false);
        parse_str($url['query'], $query);

        foreach ($query as $key => $value) {
            $request->query($key, $value);
        }

        return $request;
    }

    /**
     * @param TestResponse $response
     * @return stdClass
     */
    public function jsonResponse(TestResponse $response): stdClass
    {
        return json_decode($this->responseContent($response));
    }

    private function responseContent(TestResponse $response)
    {
        return $response->baseResponse instanceof StreamedResponse ? $response->streamedContent() : $response->getContent();
    }

    public function req(Request $request): TestResponse
    {
        return $this->call(
            $request->method,
            $request->uri(),
            [],
            [],
            [],
            $this->transformHeadersToServerVars($request->headers),
            $request->body,
        );
    }

    protected function assertXmlResponse(Request $request)
    {
        $response = $this->req($request);
        $this->assertMatchesXmlSnapshot($this->responseContent($response));
        $this->assertResponseMetadata($response);
    }

    protected function assertResponseMetadata(TestResponse $response)
    {
        $this->assertMatchesSnapshot([
            'headers' => array_diff_key($response->baseResponse->headers->all(), array_flip(['date'])),
            'status' => $response->baseResponse->getStatusCode(),
        ]);
    }

    protected function assertMetadataResponse(Request $request)
    {
        $response = $this->req($request);
        $this->assertResponseMetadata($response);
    }

    protected function assertJsonResponse(Request $request): TestResponse
    {
        $response = $this->req($request);
        $this->assertMatchesSnapshot($this->responseContent($response), new JsonDriver());
        return $response;
    }

    protected function assertHtmlResponse(Request $request): TestResponse
    {
        $response = $this->req($request);
        $this->assertMatchesHtmlSnapshot($this->responseContent($response));
        return $response;
    }

    protected function assertTextResponse(Request $request): TestResponse
    {
        $response = $this->req($request);
        $this->assertMatchesTextSnapshot($this->responseContent($response));
        return $response;
    }

    protected function assertProtocolException(ProtocolException $e)
    {
        $this->assertMatchesSnapshot($e->serialize());
    }

    protected function assertJsonMetadataResponse(Request $request)
    {
        $response = $this->req($request);
        $this->assertMatchesSnapshot($this->responseContent($response), new JsonDriver());
        $this->assertResponseMetadata($response);
    }

    protected function assertTextMetadataResponse(Request $request)
    {
        $response = $this->req($request);
        $this->assertMatchesTextSnapshot($this->responseContent($response));
        $this->assertResponseMetadata($response);
    }
}
