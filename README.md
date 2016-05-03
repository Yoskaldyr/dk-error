# dk-error

This package contains some classes and interfaces to standardize middleware errors in a psr7(zend-expressive) context.
It contains a general `Error` class, an `AbstractErrorHandler` class, to be put in the error pipeline and a `ErrorResponseStrategy` interface to define strategies for the error handling logic.

This package is used mainly in the dk-{package} libraries, it can be optionaly used in a specific project, or you could write your own error handlers.

### Usage
The following steps can be used in order to integrate this package into your project, or use it in your libraries
* Extend the abstract class `AbstractErrorHandler`. Here, you could leave it empty, and just call `parent::__invoke`. This class must be injected with a ErrorResponseStrategy, so you should also create a factory class, if used through a DI container.
* Create a strategy class that implements `ErrorResponseStrategy` and feed it to the ErrorHandler. This class is responsible for returning a ResponseInterface type of class based on the error passed to it. This is the piece where you usually put your custom error handling logic and return the error response in various ways based on the error code or even request/response bits of data(redirects, json responses, etc.)
* Optionally, create more specific types of errors, by extending the `Error` class

### Error class

Simple class holding having a `code`, `title`, `type`, `message`, `extra` fields. The only required parameter is the code.
* code - integer value describing the error(usually HTTP codes)
* title - short error message(reason phrase)
* type - a link to a document describing the error types(usually HTTP link to status codes)
* message - string describing the error in detail
* extra - any data that could offer more details on the error, or carry more info

The class has only getters. Initialization is made through the constructor, which has the following signature
```php
    /**
     * Error constructor.
     * @param int $code
     * @param string|null $title
     * @param string|null $type
     * @param string|null $message
     * @param mixed|null $extra
     * @throws \Exception
     */
    public function __construct($code, $title = null, $type = null, $message = null, $extra = null)
    {
```

As this class will be used to send errors to handlers and create psr7 responses based on them, it is usually used with only a code and a message(extra maybe), and let the title and type to their default values created by the psr7 response,

Some examples are:
```php
//using the base class
$error = new Error(401, null, null, 'Authentication failed');
//better extend this class to define a specific type of error

//suppose AuthenticationError extends Error with an overwritten constructor 
$authError = new AuthenticationError(401, 'Authentication failed');
```

### AbstractErrorHandler class

Abstract error handler middleware, to handle Error type errors. It has the error handler __invoke signature as required by zend-expressive
```php
public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null)
```

The error handler flow can be summarized to the following points:
* If $error is not of type `Error`, it will skip to the next error handler if present, or return the response directly.

* Else, it checks if a response strategy is defined, and calls that, expecting a ResponseInterface implementation or a falsy return value.

* if the response strategy is not defined, or the strategy's response is falsy, it will go to the next error handler, but not before modifying the response status code to match the error's code.

* if response strategy returned a valid response, it will return that instead and terminate the pipe.

When extending this class, you have 2 options
* ignore the optional response strategy, and implement your own logic in the `__invoke` method, before calling `parent::__invoke`. You will catch your custom errors for example, and let other errors flow through the pipe, possibly reaching the final handler.

* define a response strategy that, based on the request,response and error objects, will return an appropriate response. This can be usefull for example in case of an api, for example returning a json representation of the error. You can also skip to the next error handler or final handler by returning null or false from the response strategy.

### ErrorResponseStrategy interface

Declares a single method that need to be implemented. Should return a valid psr7 response or false/null if you want the error handler to ignore it

```php
    public function createResponse(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Error $error);
}
```


