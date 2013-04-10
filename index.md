---
layout: default
---

> #### Notice
> The Vanilla API is still undergoing heavy development and gets rewritten every so often. Do therefore __not__ use it for __anything__ just yet. You're more than welcome to take a look at the source though and any contribution would be greatly appreciated!

## Getting started

To get started using Vanilla API, either:
- [Download the latest release](https://github.com/kasperisager/VanillaAPI/archive/master.zip)
- Clone the repository directly into your Vanilla `applications` directory:  
{% highlight sh %}
$ cd /path/to/vanilla/applications/
$ git clone git://github.com/kasperisager/VanillaAPI.git
{% endhighlight %}

When you've done this, make sure the newly created folder is named `api` and not `VanillaAPI`. Now simply go to your dashboard, enable the API in the "Applications" menu and you're all set!

### Generating documentation

The application source is well-documented and the API comes bundled with [Sami](https://github.com/fabpot/Sami) for generating the documentation. You will however need to install Sami first after which you can generate the documentation:

{% highlight sh %}
$ composer install
$ php vendors/sami/sami/sami.php update config.php -v
{% endhighlight %}

If the Composer installation fails, if might be a good idea to [read the instructions](http://getcomposer.org/doc/00-intro.md#installation-nix) on how to use Composer. When you've got it all up and running, navigate to http://your-domain.com/applications/api/build to read the generated documentation.

## How does it work?

Vanilla API is in fact not an API in itself. A more fitting description would be that it's a mapper tool whose purpose is to implement a RESTful URI scheme upon with you can invoke different methods using the standard GET, POST, PUT and DELETE HTTP verbs. It also translates PUT and DELETE requests into POST requests so that Garden can understand and process these.

In the end, you can look at Vanilla API as being an application that sits of top of the default API, translating and handling different requests for use by the application and core controllers who in return carry out the actual methods.

## Configuration

The API can be configured through the dashboard using the "Application Interface" menu found in the "Forums Settings" section. Here you can see the main API endpoint and you can re-generate the application secret used for signature based authentication. Please be aware that there's no way for you to manually set the application secret in the dashboard - it enforces using a UUID v4 key which is a randomly generated, cryptographically secure string.

Should you wish to change to request expiration time, you can do this using the following configuration statement:

{% highlight php %}
<?php $Configuration['API']['Expiration'] = 5 * 60; // Defaults to 5 minutes
{% endhighlight %}

## Authentication

Vanilla API supports two different authentication methods: A semi-stateless session based method as well as a stateless signature based method. The two are completely compatible so you are free to chose between one or other or use them both for different aspects of the same application. Both methods are also highly secure so you won't necessarily need to perform them over HTTPS although it _is_ highly recommended doing so.

### Session based

There's nothing too fancy about the session based authentication method from an API point of view: This method is in fact just the default Vanilla authentication. It is semi-stateless in the sense that when authenticating with the server, each client gets assigned a cookie which initiates a session. If a client accesses the API with a valid session, he or she can freely interface with Vanilla using the API, only restricted by their permissions.

Should you wish to use the session based authentication method, you'll need to use one of the many SSO solutions available for Vanilla to authenticate users from within your application. I have a custom SSO API class planned but it's not going to be ready anytime soon.

### Signature based

The signature based authentication method is similar to that used by [Amazon Web Services](http://aws.amazon.com/articles/1928#HTTP). To make an API call, you'll need 3 things: A public key, a private key and a signature (also referred to as _token_). The public key can be either the username or the email of the user making the request whilst the private key is an application secret generated for you upon enabling Vanilla API. The signature is an HMAC-SHA256 hash created by combining the public and the private key with any queries you want to pass along with your request and then signing it all with a Unix timestamp (UTC).

The request is then sent along to the server and the signature recreated using the information sent with the request. The server then compares its generated signature with the one from the request - if these match, then the client is considered legitimate and an authenticated session is started for the duration of the request.

#### Implementations

So far, the only implementation of the token generator is written in PHP - porting it to other languages should be pretty straight forward as the token generation is pretty simple, yet highly efficient. Let's take it by example...

Say we wanted to send the following request from our application to Vanilla:

{% highlight sh %}
METHOD /api/endpoint/:id?query=value
{% endhighlight %}

This is of course just an abstraction of an actual HTTP request. Still, according to what we read earlier we'll need to also include a public key and a timestamp:

{% highlight sh %}
METHOD /api/endpoint/:id?query=value&username=johndoe&email=example@mail.com&timestamp= [timestamp]
{% endhighlight %}

It's not required that you include both a username _and_ an email, but let's do it anyway. The request above would correspond to the following data array:

{% highlight php %}
<?php
$Request = array();
$Request['query'] = 'value';
$Request['username'] = 'johndoe';
$Request['email'] = 'example@mail.com';
$Request['timestamp'] = [timestamp];
{% endhighlight %}

Next up, the actual magic: An HMAC token. First off, we'll need to take our request apart as we'll need to do a little sorting of the request parameters so we're sure the token is generated the same way on both client and server. More specifically, we'll need to sort the parameters alphabetically after which we remove all the keys (we're only interested in the values) and delimit the values with a dash:

{% highlight php %}
<?php
// Sort the request data alphabetically
ksort($Request, SORT_STRING);

// Delimit the data values with a dash
implode('-', $Request)
{% endhighlight %}

Lastly, we can use the request data to generate an HMAC token using our application secret:

{% highlight php %}
<?php
$Token = hash_hmac('sha256', strtolower($Request), $Secret);
{% endhighlight %}

It's important that we lowercase the request data as to ensure consistent hash generation. We can now add the token to our request and send it off to the server:

{% highlight sh %}
METHOD /api/endpoint/:id?query=value&username=johndoe&email=example@mail.com&timestamp= [timestamp]&token=[generated hash]
{% endhighlight %}

#### Security precautions

It is highly recommended to include the HTTP method as well the request URI in your query, and thus in the hash generation as well, as this can help prevent man-in-the-middle attacks where an attacker could potentially modify the endpoint you are operating on as well as the HTTP method.

## Extending

Vanilla API allows you to easily integrate your own plugins and applications with the API Mapper - it's as simple as creating a new API class and putting it anywhere in your application or plugin where the Garden autoloader can find it. You can also write your own autoloader which is what I've done for loading the core API classes - this merely because I'm pretty nit-picky when it comes to my folder structure.

### Classes

As an exmaple, say we wanted to integrate our application or plugin's custom API (let's name it _Foo_) with the Vanilla API Mapper so that we can access our API in a RESTful manner. What we'd do is create a file named `class.api_class_foo.php` and put it in, say, our application or plugin's `library` directory. Just to get things going, let's go ahead and write the class skeleton:

{% highlight php %}
<?php if (!defined('APPLICATION')) exit();

/**
 * Foo API
 *
 * Description of Foo API
 *
 * @package    [name]
 * @since      [version]
 * @author     [author] <[email]>
 * @copyright  [description]
 * @license    [url] [description]
 */
class API_Class_Foo extends API_Mapper
{
   // API operations go here
}
{% endhighlight %}

It's super important that your API class follows the PEAR naming conventions as the API engine calls classes by taking the URI resource path (a call to `api/foo` would results in a URI resource path of `foo`) and prefixing it with `API_Class_`. This is a pseudo-namespace technique that allows us to avoid class name clashes.

### Operations

Now that we've written the class skeleton, it's time to write some API methods (operations). The API Mapper has four methods that you can extend: `Get()`, `Post()`, `Put()` and of course `Delete()`. You don't have to extend them all - should to chose to only extend, say, the `Get()` method since your API is read-only, the three others will simply throw a 501 Method Not Implemented.

Each method must return an array of data containing at least a `Controller` key which defines the controller that you'd like to map the API request to. By default, the controller's `Index` method will be called but you can override this by adding a `Method` key to the data array. Say, for example, that our application or plugin's `Foo` controller defines the method `Bar` that allows us get a list of... well, FooBars? If we want to make that resource universally available by sending a `GET` request to `api/foo`, this would be our `Get()` method:

{% highlight php %}
<?php
// Each API method takes a $Path array containing
// some the full URI of the request
public function Get($Path)
{
   // Set up the array that we wish to return
   $Return = array();

   // Set the 'Controller' key
   $Return['Controller']   = 'Foo';
   
   // Set the 'Method' key
   $Return['Method']       = 'Bar';

   // Return our $Return array
   return $Return;
}
{% endhighlight %}

Et voilá! Requesting `api/foo` will now give us a JSON or XML document (depending on our HTTP_ACCEPT header) containing our FooBars. What if we wanted to get a specific FooBar when requesting `api/foo/:id`? For this purpose, we can use the `$Path` variable since it contains the full URI with `api` being the index (0), `foo` being the first value (1) and `:id` being the second value (2). Let's start out by reworking our `Get()` method so it knows what function to call when a client requests a specific FooBar:

{% highlight php %}
<?php
public function Get($Path)
{
   // If an ID is defined, store it
   if (isset($Path[2])) $ID = $Path[2];
   
   if (isset($ID))
      return self::GetById($ID);
   else
      return self::GetAll();
}
{% endhighlight %}

Now on to writing the `GetAll()` function which was the one we initially wrote:

{% highlight php %}
<?php
public static function GetAll()
{
   $Return = array();
   $Return['Controller']   = 'Foo';
   $Return['Method']       = 'Bar';

   return $Return;
}
{% endhighlight %}

Finally, we can write the `GetById()` function:

{% highlight php %}
<?php
public static function GetById($ID)
{
   // Same old, same old
   $Return = array();
   $Return['Controller']   = 'Foo';
   $Return['Method']       = 'Bar';
   
   // Add the ID as a method argument
   $Return['Arguments']    = array($ID);

   return $Return;
}
{% endhighlight %}

And that's it! Requesting `api/foo/:id` will now get us a specific FooBar given that you've implemented getting resources by ID in your `Bar` function in your `Foo` controller.

As for the rest of API operations, it's pretty much the same as the basic `Get()` method. There's one slight difference: The rest of the function allow you to define custom Form Data arguments to be passed along to a given controller. To do this, simply add the custom data to the `Arguments` key:

{% highlight php %}
<?php $Return['Arguments']['Custom Key'] = 'Custom Value';
{% endhighlight %}

## Issue tracking

If you come across any bugs or if you have a feature request, please [file an issue](https://github.com/kasperisager/VanillaAPI/issues) using the Github Issue tracker. Vanilla API won't be supported through http://vanillaforums.org so please stick to using Github for inquires about bugs and feature requests. Thanks!
