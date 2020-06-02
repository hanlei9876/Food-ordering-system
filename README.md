**Requirements gathered**

1\. Restaurant’s info to display

  - Restaurant title

  - Address

  - Contact phone

  - Promo Photo for the restaurant

2\. Food are organized based on Categories bar, each dish
contains:Details, without photos. For the style design, we can reference
to his restaurant at Menulog
<https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu>

And two other restaurants:

<https://www.orderfromus.com.au/thaiwaterfront/>

<https://seasandthairestaurant.com.au/#!pg=-99&n=Trending>

3\. Functionalities to inplement

(1) customer login

Each user has a total consumption amount that will be treated as loyalty
points (encouragement policy)

(2) customer can still order food without login

(3) info required for login

  - Name,

  - phone,

  - email,

  - residential address (refer to Waterfront restaurant)

~~(4) promo code (CANCELED)~~

  - Check-out has promotion

  - Homepage has promo info and promo code

  - (refer to Sea Sand Thai, and Waterfront)

(5) Shopping cart has two options:

  - delivery (paying cash is available)

  - collection

(6) Payment methods:

  - Cash

  - Card payment

**  
**

**Online Food Ordering System for Tom**

1.  MAMP & Wordpress: <https://zhuanlan.zhihu.com/p/32473851>

2.  Restropress: Search, install and activate plugin Restropress.
    Disable sidebar in current Wordpress Theme.

1\. 用wordpress在本地来搭建网站。建好通过测试后，在migrate到服务器上host起来，这样clients就可以访问此网站了。

2\. **MAMP**

**简介**

MAMP 是的基于PHP的web 开发环境，集成了 Apache、MySQL
DB、PHP，安装即用，可以很方便的在本地电脑上安装我们需要的应用。

  - **官网：**<https://www.mamp.info/en/mamp/mac/>

<!-- end list -->

  - **平台：** OS X

**MAMP** is a free and open-source, Mac-platform based web server
solution stack.

Note:

  - Platform = Operating system

  - Solution = software

  - In computing, solution stack = software stack. A solution stack is
    **a set of software subsystem/components** needed to create a
    **complete** platform. That is, to develop the software, no other
    components are needed. A software platform supports applications to
    run on it. 其实，就是开发一个平台系统所需的**一整套components**。

所以，XAMPP is designed for developing web servers (system), being composed
of

  - Support Mac

  - Apache HTTP Server

  - MySQL Database Management System

  - Interpreter of PHP

3\. WordPress

It’s an open-source software to create website, blog and app.

Installation Steps

1\. download MAMP and install it on my MAC.

![](media/image1.png)I unchecked MAMP PRO, which is not free.

2\. After successful installation, MAMP folder appears in the
Application folder:

![A picture containing screenshot Description automatically
generated](media/image2.png)

3\. Start MAMP with the Elephant button in this MAMP folder.

4\. Set ports for Apache and MySQL as default

Preference -\> Port

![A screenshot of a cell phone Description automatically
generated](media/image3.png)

Editing the post settings to one of two options:

  - Keep the Apache port setting at the default of 8888, which means
    that when you visit your local site, you’ll have to type the port
    number in the browser.

  - Change the Apache port setting to 80, which means you won’t have to
    type the post number in the browser but does mean that whenever MAMP
    launches, it will ask you for your computer’s password.

Which of these you choose makes no difference to how MAMP runs or how
your website will run: it’s just a choice between whether you want to
keep typing 888 in the browser or you’d prefer to enter your password.

5\. configure web server and document root

![](media/image4.png)

Make sure to select Apache server, instead of Nginx server.

Leave document root as default.

The document root is where **all your files for your local web server**
will be stored. You are simply specifying where MAMP will be looking for
the files.

6\. start the server, the browser will automatically starts as well and
go to this page below.

Every time I leave the development, just stop the server.

![A screenshot of a cell phone Description automatically
generated](media/image5.png)

7\. go to phpMyAdmin (GUI administrator for MySQL database)

<http://localhost:8888/phpMyAdmin/?lang=en>

Then Create a database, which the to-be-installed wordpress will live
on.

Note: Every time we want to install a wordpress, we need to create a new
database.

![A screenshot of a social media post Description automatically
generated](media/image6.png)

![A screenshot of a social media post Description automatically
generated](media/image7.png)

My database name: ‘foodOrdering\_db’

8\. install wordpress

Step-1: Now, find the WordPress installation .zip file we downloaded in
step one and unzip it. You’ll get a “wordpress” folder. Rename it to
something relevant to the site you will be building/testing locally.
(I’m going to name mine “foodOrdering.”)

Grab that folder and store it in your htdocs folder.

![A screenshot of a cell phone Description automatically
generated](media/image8.png)

Step-2: go to <http://localhost:8888/foodOrdering>

Below came up.

![A screenshot of a cell phone Description automatically
generated](media/image9.png)

Set up database connection for wordpress

![A screenshot of a cell phone Description automatically
generated](media/image10.png)

![](media/image11.png)Then set this up.

![](media/image12.png)This shows successful installation.

9\. login to wordpress

<http://localhost:8888/foodOrdering/wp-login.php>

![A screenshot of a social media post Description automatically
generated](media/image13.png)

10\. install RestroPress plugin to WordPress

![A screenshot of a cell phone Description automatically
generated](media/image14.png)

Then activate the plugin

![A screenshot of a cell phone Description automatically
generated](media/image15.png)

Once the plugin is installed then you can see RestroPress on the left
navigation bar of WordPress Dashboard.

![A screenshot of a social media post Description automatically
generated](media/image16.png)

Now, to be quick, we just go to the Goddady hosting
<span class="underline">wucraft.com.au</span> that comes with both

  - WordPress and

  - mySQL database.

Then, we install the plugin ‘Restropress’ inside WordPress, and start to
test email (me) and payment (pengbo).

GoDaddy (Proxy Server, providing custom domain name)

WordPress (software to make websites & personal blogs)

RestroPress:

  - RestroPress is a Online Food Ordering system for WordPress.

<!-- end list -->

  - It is a standalone WordPress plugin which allows you to easily add
    Food Ordering System to your WordPress Website.

  - Using RestroPress you can easily receive both PickUp and Delivery
    orders.

1\. email testing

![A screenshot of a social media post Description automatically
generated](media/image17.png)

![](media/image18.png)

![A screenshot of a social media post Description automatically
generated](media/image19.png)

The demonstration of email sending:

![A screenshot of a cell phone Description automatically
generated](media/image20.png)

2\. payment checking (by Pengbo)

![A screenshot of a cell phone Description automatically
generated](media/image21.png)

Both testings are successful.

Stage – 1: upload all menus and categories from menulog

Me: from Special to
    Seafood

1)  [Special](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat0)

2)  [Party
    Tray](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat1)

3)  [Entrees](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat2)

4)  [Western
    Dishes](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat3)

5)  [Chicken](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat4)

6)  [Beef](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat5)

7)  [Pork](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat6)

8)  [Duck](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat7)

9)  [Seafood](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat8)

Pengbo: from Vegetarian to
    Drinks

1)  [Vegetarian](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat9)

2)  [Noodle](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat10)

3)  [Omelette](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat11)

4)  [Rice](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat12)

5)  [Soup](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat13)

6)  [Miscellaneous](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat14)

7)  [Desserts](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat15)

8)  [Drinks](https://www.menulog.com.au/restaurants-tang-tang-canton-kitchen/menu#cat16)

Remember to add emoji to indicate spicy/vegetarian.

1\. first create a new page at WordPress

![A screenshot of a cell phone Description automatically
generated](media/image22.png)

The page name – Menu

The page tag must be set to - \[fooditems\], so RestroPress can
automatically link new food items onto this page.

2\. A challenge – sorting the categories on Menu page

By default, all the categories are sorted in Alphabetic order. We want
to give the popular categories higher priorities for display. So, we
need to customize sorting methods.

WordPress offers APIs, called ShortCode, allow us customize the
functionalities on our website. And even, plugins for WordPress, like
RestroPress, also provide ShortHand allowing us doing custom things on
the Menu page.

On the official webpage of RestroPress plugin:

![A screenshot of a cell phone Description automatically
generated](media/image23.png)

So, for solving my sorting problem,

First, I need to change the sorting criteria to “sorting against Slugs”,
like below:

![A screenshot of a computer Description automatically
generated](media/image24.png)

Second, on the page – ‘all categories’, edit it as below:

![A screenshot of a cell phone Description automatically
generated](media/image25.png)

It works.

**<span class="underline">Why we need ShortCode APIs?</span>**

1\. To avoid 恶意攻击.

WordPress filters all content to make sure that no one uses posts and
page content to **insert malicious code in the database**. This means
that you can write basic HTML in your posts, but you cannot write PHP
code.

But what if you wanted to run some custom code inside your posts to
display related posts, banner ads, contact forms, galleries, etc? This
is where Shortcode API comes in.

2\. For the purpose of version updating

WordPress and RestroPress often update their versions. If we dive into
our source code of WordPress and RestroPress to modify functionalities,
when we update a new version for WordPress and RestroPress, our modified
codes will be covered by the new code, which is not what we expect.

So, the best way to avoid the two problems above is use ShortCode APIs
provided by the official WordPress and RestroPress.

**How to Remove Author and Date Info from Your WordPress Posts**

The problem I had is as below:

![A screenshot of a cell phone Description automatically
generated](media/image26.png)

We can check the corresponding HTML code below:

![A screenshot of a social media post Description automatically
generated](media/image27.png)

So, the solution is to add external CSS code as below:

/\* Remove meta data \*/

.entry-meta .byline, .entry-meta .cat-links { display: none; }

.entry-meta .posted-on { display: none; }

![A close up of food Description automatically
generated](media/image28.png)

![A close up of food Description automatically
generated](media/image29.png)

**How to change the warning content when “not selecting your
postcode”?**

![](media/image30.png)

Originally, this warning information isn’t appropriate.

So, we add external css and changed it to:

![A screenshot of a cell phone Description automatically
generated](media/image31.png)

1\. On WordPress, plugins I have installed:

1)  RestroPress

2)  User Registration

3)  ~~My Custom Functions (Once install and activate it, the UI is at
    WordPress-\> settings-\> php inserter)~~

4)  RestroPress Delivery Fee – Single Site (Purchased)

5)  ~~Order Time, Intervals & Limits - Single site (Purchased)~~

6)  ~~PayPal Pro and PayPal Express – Single site (Purchased 49 USD)~~

7)  All-in-One WP Migration

8)  Stripe Payment Gateway (Purchased 49USD)

9)  SMS Notification – Single Site (Purchased 49USD)

The great benefit of using plugins is every time you change your
WordPress Theme or Upgrade your WordPress or plugins, the settings you
made within your plugins will always be there and follow you.

2\. The default admin email in WordPress:

<z7y2h63fj9tw@sg2plcpnl0049.prod.sin2.secureserver.net>

Now I have changed it to my own. The admin email is used to receive
Contact Form submission, as below:

![A screenshot of a social media post Description automatically
generated](media/image32.png)

**How did I successfully realize the following effect?**

![A plate of food on a table Description automatically
generated](media/image33.png)

When I scroll down:

![A screenshot of a cell phone Description automatically
generated](media/image34.png)

In order to realize the effects above, I need to work with HTML and CSS.
Below is what I modified:

1\. HTML

![A screenshot of a social media post Description automatically
generated](media/image35.png)

The code text as below:

2\. Add external CSS

![A screenshot of a cell phone Description automatically
generated](media/image36.png)

3\. go to theme’s style.css

![A screenshot of a cell phone Description automatically
generated](media/image37.png)

![A screenshot of a cell phone Description automatically
generated](media/image38.png)

![](media/image39.png)

If I want to change styles only in tablets and mobiles, do as below:

**Summary: How to use CSS to control style?**

Remember always referring to CSS Selectors Reference

1\. Locate the element to style in HTML

  - .class\_name

  - \#ID (this is unique, so it’s very precise)

  - h5 label (a label on Level-5)

Actually, any method above is OK, as long as you can locate that HTML
element.

2\. change the parameters of this element

**Migration**

1\. Ask Zicci to buy a domain with domain name (www.tangtang.com.au) at
GoDaddy.

  - Domain name is only the name (DNS), which means you can buy a domain
    name at GoDaddy, then buy a hosting somewhere else.

  - No need to buy a hosting anymore, as we can use their previous
    hosting that can host two websites (wucraft and food-ordering)

  - What we need to do for migration is set up the new domain, and make
    it redirect to the original domain name of foodordering
    (wucraft.com.au/foodordering/)

Below is how to set up in GoDaddy:

![A screenshot of a cell phone Description automatically
generated](media/image40.png)

(1)
    HTTP返回码中301与302的区别

301，302 都是HTTP状态的编码，都代表着某个URL发生了转移，不同之处在于： 

  - 301 redirect: 301 代表永久性转移(Permanently Moved)。

  - 302 redirect: 302 代表暂时性转移(Temporarily Moved )。302转向可能会有URL规范化及网址劫持的问题。可能被搜索引擎判为可疑转向，甚至认为是作弊。

(2) redirect with masking

If without masking, When type
[www.tangtang.com.au](http://www.tangtang.com.au), we will see below:

![A close up of a screen Description automatically
generated](media/image41.png)

If choose to be with masking:

![A screenshot of a cell phone Description automatically
generated](media/image42.png)

![A close up of food Description automatically
generated](media/image43.png)

![A screenshot of a cell phone Description automatically
generated](media/image44.png)

**<span class="underline">Real Migration</span>**

Eventually, if installing SSL certificate, we are required to have both
a domain and a hosting. So, Zicci has to buy a new hosting under the
domain [www.tangtant.com.au](http://www.tangtant.com.au).

And we used a WordPress plugin – **All-in-One WP Migration** to realize
WordPress system migration from
<span class="underline">wucraft.com.au/foodordering/</span> to
[www.tangtant.com.au](http://www.tangtant.com.au).

Website migration backup:

From
[www.wucraft.com.au/foodordering/](http://www.wucraft.com.au/foodordering/)

To [www.tangtang.com.au](http://www.tangtang.com.au)

![A screenshot of a social media post Description automatically
generated](media/image45.png)

**Install SSL Certificate to the website**

Ask Zicci to buy a SSL Certificate at GoDaddy for the new domain name.
So, when loading the website, google chrome will NOT identify it as
unsecure.

**Communications history with Zicci**

1\. add tax to subtotal – NO

As this will incur some additional redundant information (their billing
address) automatically showing on the checkout page, which is not what
Zicci wants.

The billing address is mandatory by the tool to follow the laws. But to
us, this this not necessary.

2\. background

  - Menulog: 15% + GST. Tang Tang use their own driver

  - UberEat: 35% + GST. UberEat provides their own driver for Tang Tang.

In general, they charge 40% of each purchase.

**Force customers to choose suburbs from 19 provided list:**

1\. The location of the WordPress system folder called
“**<span class="underline">public\_html</span>**” at:

![A screenshot of a computer Description automatically
generated](media/image46.png)

2\. The file location in the WordPress system folder
is

/public\_html/wp-content/plugins/restropress-delivery-fee/includes/templates

The file to modify:

![A screenshot of a cell phone Description automatically
generated](media/image47.png)

![A screenshot of a cell phone Description automatically
generated](media/image48.png)

**Payment Gateway Setting – PayPal Pro **

**Not applicable in Australia**

1\. what we need for PayPal API?

For PayPal Standard

![](media/image49.png)

For PayPal Pro

![A screenshot of a cell phone Description automatically
generated](media/image50.png)

**To accept PayPal payments on your website, you or your developer will
need API
credentials.**

[*https://developer.paypal.com/docs/classic/api/apiCredentials/\#creating-an-api-signature*](https://developer.paypal.com/docs/classic/api/apiCredentials/#creating-an-api-signature)

*<span class="underline">Create and Manage NVP/SOAP API
Credentials</span>*

  - When you call PayPal NVP/SOAP APIs, you must authenticate each
    request through a set of API credentials.

  - PayPal associates these credentials with a PayPal account.

  - You can generate credentials for any PayPal Business or Premier
    account.

*<span class="underline">Types of credentials</span>*

1)  API certificates - Contain the API username and password and the
    certificate.

2)  **API signatures** (we need) - Contain the API username and password
    and the signature.

*<span class="underline">To create an API signature:</span>*

1)  For live credentials, log in to your PayPal business account at
    www.paypal.com.

2)  Click the settings icon at the top of your PayPal account page and
    then click Account Settings.

3)  On the Account access page, click Update for the API access item.

4)  Click Manage API Credentials in the NVP/SOAP API Integration
    (Classic) section.
    
    *Note: If you have already generated an API signature, clicking
    Manage API Credentials displays the signature information. If you
    must generate an API signature, click Remove to delete the existing
    API signature.*

5)  Select Request API signature. Then, click Agree and Submit.

So, no matter which PayPal version we use,

1\. we need to have a PayPal business account

  - Username: tangtangcantonkitchen@gmail.com

  - Password: tangtang2019

![A screenshot of a cell phone Description automatically
generated](media/image51.png)

2\. To use either PayPal Standard or PayPal Pro, we need to have

  - PayPal Live API Username

  - PayPal Live API Password

  - PayPal Live API Signature

*NOTE: “Live APIs (using PayPal real account)” means real APIs, vs.
“Testing APIs (using PayPal sandbox account)” *

3\. To use PayPal Pro, one more piece of information is needed:

PayPal Live PayPal Merchant ID: **X99NDRGJ4FLL4**

1)  You can find your Merchant ID in your Account Profile in PayPal,

2)  This is only required for In-Context Checkout

Seems like, the In-Context Checkout is only for PayPal Express.

4\. Now, I request API credentials in the PayPal business account, as
below:

![A screenshot of a social media post Description automatically
generated](media/image52.png)

Here is we I get:

![A screenshot of a social media post Description automatically
generated](media/image53.png)

  - Credential - Signature

  - API username - tangtangcantonkitchen\_api1.gmail.com

  - API password - 7VVUVLX932ZS9797

  - Signature - AoiCBNGVMlgj2ndAkLS-nFk5d9sfA2ymi5QYvNFABHWviXmOgOm7tckO

  - Request date - 26 April 2020 at 1:05:25 PM AEST

**  
**

**Payment Gateway Setting – Stripe**

![A screenshot of a cell phone Description automatically
generated](media/image54.png)

Asked Zicci ro register a Stripe account and we can get the information
below from the account:

![A screenshot of a cell phone Description automatically
generated](media/image55.png)

If the restaurant wants to make **refund** to customer, the restaurant
can go to Stripe account, and do the operations inside.

**Zicci add the website to Google My Business
Website**

[**https://tangtangcantonkitchen.business.site**](https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxcheckurl?requrl=https%3A%2F%2Ftangtangcantonkitchen.business.site&skey=%40crypt_8f9fa21b_5fec18b5d85199bec8837adffbd25749&deviceid=e056997574714611&pass_ticket=undefined&opcode=2&scene=1&username=@497629d6b685d8da54bdc1b05c27cbac)

![A screenshot of a social media post Description automatically
generated](media/image56.jpeg)

**The ultimate effect is below: In Google search engine, when typing the
restaurant name, you will have below:**

![A screenshot of a social media post Description automatically
generated](media/image57.png)

**  
**

**Training**

*<span class="underline">Prerequisites</span>*:

1\. buy SSL certificate for your domain (www. Tangtnag.com.au) + a new
hosting

2\. have an admin email: to send purchase receipt from

3\. have an email

  - to receive customer order notification

  - to receive message by customer at the Contact Us page

~~4. Must have a PayPal Pro Business account to receive any online
payment from customers~~

4\. Must have a Stripe Business account

Where To set up an email address to receive customers’ feedback on
“Contact Us” page?

![A screenshot of a cell phone Description automatically
generated](media/image58.png)

*<span class="underline">Key points to cover:</span>*

how a customer will

  - order different types of food (delivery/pickup)

  - all the way to payment (cash-on-delivery/card payment).

  - Then the customer will receive email

  - Register and login to check his order history and order food

How the restaurant use the dash board will

  - receive notification email, and will

  - check order at admin dashboard, and

  - change status of the order

  - receive card payment (if any)

Business hours:

![A screenshot of a cell phone Description automatically
generated](media/image59.png)

Based on this setting above, when we place an order after business
hours, say 10:00pm, we will get

![A screenshot of a cell phone Description automatically
generated](media/image60.png)

How admin use the dashboard can

  - Update their menu

<!-- end list -->

  - Check order history

  - Check purchase customers

  - Check registered users

<!-- end list -->

  - publish promotion code

  - Do NOT update and plugins

**Something to improve & optimize**

1\. When loading the landing page, it’s too slow as the header image’s
size is too large (11 MB).

So, I compressed the image using Lightroom to 980KB. To make loading
faster. (**Solved\!**)

2\. If the order is under 25aud, we will not allow custom to place an
delivery order. And only pickup is available. (**Solved\!**)

***<span class="underline">Strategy</span>***:

First, we enable the system configuration option in RestroPress, that is
to restrict users to order for a minimum amount.

![A screenshot of a social media post Description automatically
generated](media/image61.png)

By default (based on experiment), the option will only examine the total
amount of order as below:

![A screenshot of a cell phone Description automatically
generated](media/image62.png)

Then, we change the option from working on Total Amount to working on
SubTotal Amount as below:

![A screenshot of a cell phone Description automatically
generated](media/image63.png)

***<span class="underline">Solution</span>***:

(1) enable the option as below:

![A screenshot of a social media post Description automatically
generated](media/image61.png)

(2) go to the file:
/Users/hanlei9876/Desktop/public\_html/wpcontent/plugins/restropress/includes/process-purchase.php

Find the original function as below

![A screenshot of a cell phone Description automatically
generated](media/image64.png)

(3) change Line-1210 to below (php code):

![A screenshot of a cell phone Description automatically
generated](media/image65.png)

Why we need to do this:

Because we found that both the original function
rpress\_get\_cart\_total() and the target function
rpress\_get\_cart\_subtotal() are from the file as
blow:

/Users/hanlei9876/Desktop/public\_html/wp-content/plugins/restropress/includes/cart/functions.php

![A screenshot of a cell phone Description automatically
generated](media/image66.png)

function rpress\_get\_cart\_subtotal() can get the subtotal.

And we use another condition: get\_delivery\_fees() \> 0. This is to
make sure “delivery option is selected.”, which will lead to a $5
delivery fee. The function get\_delivery\_fees() comes from this file
below:

/Users/hanlei9876/Desktop/public\_html/wp-content/plugins/restropress/templates/
widget-cart-checkout.php

![A screenshot of a cell phone screen with text Description
automatically generated](media/image67.png)

So, eventually, the result is as below:

![A screenshot of a cell phone Description automatically
generated](media/image68.png)

3\. Order Notification problem:

Email notification of order is a bit slow sometimes, not instant\!

SMS notification plugin is not free, and doesn’t work, and a bit slow
(from comments on it by people) as well.

So, we tried to work out the “Order Notification” as below

![A screenshot of a cell phone Description automatically
generated](media/image69.png)

But it doesn’t work. So, we fixed that (**Solved\!**).

**Solution:**

1\. Our research in order to figure out solutions:

1)  We googled this problem with RestroPress, and found this question
    asked by other people. But didn’t get a clear solution.

2)  We dived into to the source code RestroPress, starting from
    front-end JavaScript to back-end PHP. And found

<!-- end list -->

  - the feature supported by the code is commented by developers as
    “still not working”. But,

  - the feature supported by the code is actually working but just is
    lack of “auto-configuration in Browser”.

The related codes are as below:

***<span class="underline">admin-scripts.js  =\> ajax-functions.php =\>
functions.php</span>***

**1. admin-scripts.js  **

![A screenshot of a computer Description automatically
generated](media/image70.png)

**We can see that, the algorithm is set to scan the order history from
database every 10 seconds\! If there exists an order with the status of
“uncompleted” (including a new incoming order), the notification is
sent to a browser.**

**2. ajax-functions**

![A screenshot of a social media post Description automatically
generated](media/image71.png)

**3.** **functions.php**

![A screenshot of a social media post Description automatically
generated](media/image72.png)

So, after understanding the code, we don’t have to revise the code, and
only need to configure the browser and operating system (Windows, Mac)
to enable display the order together with playing the uploaded sound.

Below is the setting for Chrome and Windows 10:

![A screenshot of a cell phone Description automatically
generated](media/image73.png)

![A close up of a map Description automatically
generated](media/image74.png)

![A screenshot of a cell phone Description automatically
generated](media/image75.png)

For the Chrome on Mac: ![A screenshot of a cell phone Description
automatically generated](media/image76.png)

![A screenshot of a cell phone Description automatically
generated](media/image77.png)

Make sure the settings on MAC:

![A screenshot of a cell phone Description automatically
generated](media/image78.png)

***<span class="underline">  
</span>***

***<span class="underline">Backup the code update</span>***:

Original version:

if( rpress\_get\_cart\_total() \< $minimum\_order\_price ) :

rpress\_set\_error( 'rpress\_checkout\_error', $minimum\_price\_error );

endif;

Updated version:

if( (rpress\_get\_cart\_subtotal() \< $minimum\_order\_price) &&
(get\_delivery\_fees() \> 0) ) :

rpress\_set\_error( 'rpress\_checkout\_error', $minimum\_price\_error );

endif;

**NOTE:**

This change happens before any DISCOUNT CODE is input, so when the
restaurant wants to give their customer a DISCOUNT CODE, they have to
explain that,

As long as you ordered items total prices \>= 25, and after discount,
the total price goes down to under 25, your order can be still placed
successfully. So, we will deliver for you and charge you extra $5
delivery fee.

*<span class="underline">  
</span>*

*<span class="underline">Things to remind Zicci:</span>*

1\. received email might goes into junk mail, please tell customer to
notice. (recommend using Gmail)

2\. In email, the payment method is only ‘cash on delivery’ when paying
cash

3\. Paying with card (This is provided by PayPal Pro) has extra charge.
**Test the real payment**

Usernames and Passwords:

![A screenshot of a social media post Description automatically
generated](media/image79.png)

WordPress Admin:

Username: zluc5j

Password: 1q2w3e

**Payment for us**

1\. Market Reference:

1)  Average market price for a web developer in Sydney: AUS 50/hr

2)  In Sydney:

<!-- end list -->

  - To make a website like wucraft.com.au: AUD 3000

  - To make an e-commerce website like this food ordering system: AUD
    6000 – 15000

2\. Our calculation:

AUD 35/hs, I and Pengbo totally spent 110 hrs,

So, the total fee should be 3850. But we only ask for **3800**.
