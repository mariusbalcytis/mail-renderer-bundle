<?php

namespace Maba\Bundle\MailRendererBundle\InlineCss;

use Emogrify\Emogrifier;

class StyleToInlineConverter
{
    protected $emogrifier;

    public function __construct(Emogrifier $emogrifier)
    {
        $this->emogrifier = $emogrifier;
    }

    /**
     * Inline CSS inside of HTML and return resulting HTML
     * @param string $html
     * @param string $css
     * @return string
     */
    public function inlineCSS($html, $css)
    {
        $this->emogrifier->setHTML($html);
        $this->emogrifier->setCSS($css);
        return @$this->emogrifier->emogrify();
    }
}
