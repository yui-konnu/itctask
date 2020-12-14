# ITC Task

This is a Laravel 8 application to display a list of products from the ITC webservice.

The essential code flow is:

The index route is defined in routes\web.php. This route will call the getProducts() method on the ApiClientService object.

The ApiClientService is defined in app\Services\ApiClientService.php. This class' main method, getProducts(), will call the list method on the webservice. This API call returns a list of product ID's. The method will then call the info API endpoint for each of these ID's.

The retryRequest() method, which executes the list/info requests, will try the HTTP request a maximum of 10 times in the event of failure. If this is exceeded then the method will throw an exception, which is then handled by the framework's error handler.

After retrieving info for each of the products, the code will call sanitiseArray() on the info array. This in turn calls sanitiseText() recursively for each non-array element in the array. This will remove any unwanted tokens/text (html/surrounding quotes/unprintable characters) from the text.

The getProducts() method will append the sanitised product info to an array, and all these products will be returned from the method.

These products are then passed to the view, which is at resources\views\products.blade.php.

## Tests

I have created an example unit test at tests\Unit\ApiClientSanitisationTest.php which just tests the text sanitisation functionality.