<?php

namespace Maba\Bundle\MailRendererBundle\Service;

use Maba\Bundle\MailRendererBundle\Converter\HtmlToTextInterface;
use Maba\Bundle\MailRendererBundle\Exception\MailUnsentException;
use Maba\Bundle\MailRendererBundle\Filter\FilterInterface;

class FormattedMessageMailer
{
    protected $mailer;
    protected $twig;
    protected $htmlToTextConverter;

    /**
     * @var FilterInterface[]
     */
    protected $htmlFilters = array();

    /**
     * @var FilterInterface[]
     */
    protected $textFilters = array();

    /**
     * @var string default email from which mails are sent
     */
    protected $defaultFromEmail;

    /**
     * @var string default name from which mails are sent
     */
    protected $defaultFromName;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        HtmlToTextInterface $htmlToTextConverter,
        $defaultFromEmail,
        $defaultFromName
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->htmlToTextConverter = $htmlToTextConverter;
        $this->defaultFromEmail = $defaultFromEmail;
        $this->defaultFromName = $defaultFromName;
    }

    public function addHtmlFilter(FilterInterface $filter)
    {
        $this->htmlFilters[] = $filter;
    }

    public function addTextFilter(FilterInterface $filter)
    {
        $this->textFilters[] = $filter;
    }

    /**
     * @param string $toEmail
     * @param string $templateName
     * @param array $context
     *
     * @return \Swift_Message
     */
    public function createMessage($toEmail, $templateName, array $context)
    {
        $template = $this->twig->loadTemplate($templateName);
        if (!$template instanceof \Twig_Template) {
            throw new \RuntimeException('Invalid template class: ' . get_class($template));
        }

        $context += array('toEmail' => $toEmail);

        if ($template->hasBlock('body_html')) {
            $htmlBody = $template->renderBlock('body_html', $context);
        } else {
            $htmlBody = null;
        }
        if ($template->hasBlock('body_text')) {
            $textBody = $template->renderBlock('body_text', $context);
        } elseif ($htmlBody !== null) {
            $textBody = $this->htmlToTextConverter->convert($htmlBody);
        } else {
            throw new \RuntimeException('Mail template must body_html or body_text block');
        }

        $subject = $template->renderBlock('subject', $context);

        $message = new \Swift_Message();
        $message->setSubject($subject);
        $message->setFrom($this->defaultFromEmail, $this->defaultFromName);
        $message->setTo($toEmail);

        foreach ($this->textFilters as $filter) {
            $textBody = $filter->filter($textBody);
        }

        if (!empty($htmlBody)) {
            $htmlBody = sprintf(
                '<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>%s</body></html>',
                $htmlBody
            );

            foreach ($this->htmlFilters as $filter) {
                $htmlBody = $filter->filter($htmlBody);
            }

            $message->setBody($htmlBody, 'text/html');
            $message->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        return $message;
    }

    /**
     * @param string $toEmail
     * @param string $templateName
     * @param array $context
     */
    public function sendMessage($toEmail, $templateName, array $context)
    {
        if ($this->mailer->send($this->createMessage($toEmail, $templateName, $context)) !== 1) {
            throw new MailUnsentException();
        }
    }
}
