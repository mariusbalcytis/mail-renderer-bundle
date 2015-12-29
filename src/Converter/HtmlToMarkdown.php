<?php

namespace Maba\Bundle\MailRendererBundle\Converter;

/**
 * Uses nickcernis/html-to-markdown to convert HTML to plain text
 */
class HtmlToMarkdown implements HtmlToTextInterface
{
    /**
     * @param string $html
     * @return string plain text
     */
    public function convert($html)
    {
        $markdown = new \HTML_To_Markdown($html);
        return strip_tags($markdown->output());
    }
}
