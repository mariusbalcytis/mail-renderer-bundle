<?php

namespace Maba\Bundle\MailRendererBundle\Converter;

interface HtmlToTextInterface
{
    /**
     * @param string $html
     * @return string plain text
     */
    public function convert($html);
}
