<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Mail;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
use JsonSerializable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zend_Mime;
use Zend_Mime_Part;

/**
 * Attachment model
 *
 * Wrapper class for binary and meta data of a file
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attachment extends \Magento\Framework\Model\AbstractModel implements JsonSerializable, AttachmentInterface
{
    /**
     *
     */
    const ZEND_MAIL_ATTACHMENT_DEFAULT_TYPE = 'application/pdf';
    /**
     *
     */
    const ZEND_MAIL_ATTACHMENT_DEFAULT_FILENAME = 'attachment.pdf';

    /** @var \Shockwavemk\Mail\Base\Model\Config */
    protected $config;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Shockwavemk\Mail\Base\Model\Storeages\Base */
    protected $storeage;

    /**
     * Attachment constructor.
     * @param \Shockwavemk\Mail\Base\Model\Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param \Shockwavemk\Mail\Base\Model\Storeages\Base $storeage
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(\Shockwavemk\Mail\Base\Model\Config $config,
                                ScopeConfigInterface $scopeConfig,
                                \Shockwavemk\Mail\Base\Model\Storeages\Base $storeage,
                                \Magento\Framework\Model\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
                                \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
                                array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->storeage = $storeage;
    }

    /**
     * Return all data, except binary
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $data = $this->getData();
        $data['binary'] = '';

        return $data;
    }

    /**
     *
     *
     * @return Zend_Mime_Part
     */
    public function toMimePart()
    {
        /** @var Zend_Mime_Part $attachmentMimePart */
        $attachmentMimePart = new Zend_Mime_Part($this->getBinary());

        $attachmentMimePart->type = $this->getMimeType();
        $attachmentMimePart->disposition = $this->getDisposition();
        $attachmentMimePart->encoding = $this->getEncoding();
        $attachmentMimePart->filename = $this->getFileName();

        return $attachmentMimePart;
    }

    /**
     * Returns binary data of a single attachment file
     *
     * @return string binary data of attachment file
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getBinary()
    {
        if (empty($this->getData('binary'))) {
            $this->setData(
                'binary',
                $this->storeage->loadAttachment(
                    $this->getMail(),
                    $this->getFilePath()
                )
            );
        }

        return $this->getData('binary');
    }

    public function getMail()
    {
        return $this->getData('mail');
    }

    /**
     *
     *
     * @return string
     */
    public function getFilePath()
    {
        if (empty($this->getData('file_path'))) {
            $this->setData('file_path',
                self::ZEND_MAIL_ATTACHMENT_DEFAULT_FILENAME
            );
        }

        return $this->getData('file_path');
    }

    /**
     *
     *
     * @return string
     */
    public function getMimeType()
    {
        if (empty($this->getData('mime_type'))) {
            $this->setData('mime_type',
                self::ZEND_MAIL_ATTACHMENT_DEFAULT_TYPE
            );
        }

        return $this->getData('mime_type');
    }

    /**
     * Returns current setting of how to integrate attachment into message
     *
     * @return string
     */
    public function getDisposition()
    {
        if (empty($this->getData('disposition'))) {
            $this->setData(
                'disposition',
                Zend_Mime::DISPOSITION_ATTACHMENT
            );
        }

        return $this->getData('disposition');
    }

    public function getEncoding()
    {
        return $this->getData('encoding');
    }

    /**
     *
     *
     * @return string
     */
    public function getFileName()
    {
        if (empty($this->getData('file_name'))) {
            return basename($this->getFilePath());
        }

        return $this->getData('file_name');
    }

    /**
     * @param string
     * @return $this
     */
    public function setBinary($value)
    {
        $this->setData('binary', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDisposition($value)
    {
        $this->setData('disposition', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setEncoding($value)
    {
        $this->setData('encoding', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setHash($value)
    {
        $this->setData('hash', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMimeType($value)
    {
        $this->setData('mime_type', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFilePath($value)
    {
        $this->setData('file_path', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSize($value)
    {
        $this->setData('size', $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value)
    {
        $this->setData('url', $value);
        return $this;
    }

    /**
     * @param string
     * @return $this
     */
    public function setMail($value)
    {
        $this->setData('mail', $value);
        return $this;
    }
}
