# Mail Renderer Bundle

Symfony bundle that collects some libraries together to make sending emails easier:

1. Renders twig template to get subject and content of an email
2. Converts HTML content to <span title="HTML to markdown conversion">plain text</span> if it's not available in template
3. Sets default From email and name to message
4. Gets CSS for mail messages using Assetic, if configured
5. Inlines CSS inside HTML `style` attributes and/or adds `<styles>` tag
6. Sends message using swift mailer

## Installation

Add via composer:

    composer require maba/mail-renderer-bundle

Register bundle:

```php
new Maba\Bundle\MailRendererBundle\MabaMailRendererBundle(),
```

Configure in `config.yml`:
    
```yml
maba_mail_renderer:
    css_assets:
        inline: mail
        imports: mail_imports
    from:
        email: info@example.com
        name: My Awesome Company
        
assetic:
    assets:
        mail:
            inputs:
                - @bootstrap_css
                - @ApplicationBundle/Resources/less/styles.less
        mail_imports:
            inputs:
                - %kernel.root_dir%/Resources/public/css/mail-imports.css
```

`css_assets` node is optional, as is usage of Assetic.

## Usage

Code:

```php
/** @var FormattedMessageMailer $mailer */
$mailer = $this->get('maba_mail_renderer.formatted_message_mailer');

$mailer->sendMessage('customer@example.com', 'ApplicationBundle:Email:welcome.html.twig', array(
    'username' => 'customer123',
    'activationLink' => 'http://www.example.com/activate/ABC123',
));
```
    
Twig template:

```twig
{# ApplicationBundle:Email:welcome.html.twig #}

{% block subject %}Hello {{ username }}, welcome to example.com!{% endblock %}

{% block body_html %}
    <div class="header">Hello {{ username }},</div>
    
    <div class="content">
        You've registered on example.com. Here you can make lots of stuff:
        <ul>
            <li>Stuff</li>
            <li>More stuff</li>
        </ul>
    </div>
    
    <div class="important">
        To activate your account <a class="btn btn-primary" href="{{ activationLink }}">click here</a>
    </div>
    
    <div class="footer">See ya!</div>
{% endblock %}
```

That's it!

All CSS rules assigned to classes will be inlined into HTML as `style` attributes.

## Extended usage

### Templates

Only these blocks are parsed from template: `subject`, `body_html` and `body_text`.
`subject` and at least one of other two are required. Any other blocks or content in template is ignored.

If `body_text` is provided, it will be used as plain text version of mailer message.

Additionally `toEmail` variable is passed to the twig template, which equals to the given recipient.

I'd suggest to use single layout twig file, if there's defined structure in your mails. For example:

```twig
{# :Email:layout.html.twig #}

{% block body_html %}
    <div class="header">Good afternoon,</div>
    
    {% block content %}{% endblock %}
    
    <div class="footer">See ya!</div>
    
    <a href="{{ get_unsubscribe_link(toEmail) }}">Unsubscribe</a>
{% endblock %}
```

```twig
{# ApplicationBundle:Email:welcome.html.twig #}
{% extends ':Email:layout.html.twig' %}

{% block subject %}...{% endblock %}
{% block content %}
    ...
{% endblock %}
```

### Styles

`inline` and `imports` values in `css_assets` configuration node take Assetic asset name, which you should configure
using Assetic configuration.

`inline` is used when applying CSS rules to message HTML result - `style` attributes are added to HTML nodes, which
would trigger appropriate CSS rules. This is needed as some email readers support neither `<styles>` nor `<link>`
(for example GMail).

`imports` is used to make `<styles>` tag with CSS contents in the mail message itself. This should be as short as
possible - usually only for rules that cannot be inlined, like this one:

```css
@import url("//fonts.googleapis.com/css?family=Ubuntu:400,400italic,700,700italic&subset=latin,latin-ext");
```

That's why it's called `imports` in configuration.

Why `<styles>` instead of `<link>`? To avoid remote resource loading, which is also most commonly disabled by default.

### Changing created message

Use `createMessage` instead of `sendMessage` with same parameters in renderer,
change or add any required fields and send with swift mailer manually.
