<?php

namespace Ano\Bundle\MediaBundle\Util\Image;

use Ano\Bundle\MediaBundle\Model\Media;
use Ano\Bundle\SystemBundle\HttpFoundation\File\MimeType\ExtensionGuesser;

use Imagine\ImagineInterface,
    Imagine\ImageInterface,
    Imagine\Image\Box;

use Gaufrette\File;

class ImagineImageManipulator implements ImageManipulatorInterface
{
    /* @var \Imagine\ImagineInterface */
    protected $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritDoc}
     */
    public function resize(Media $media, File $fromFile, File $toFile, $width, $height, $mode = self::RESIZE_MODE_OUTBOUND, $options = array())
    {
        if (empty($width) && empty($height)) {
            throw new \InvalidArgumentException('You must specify at least a width and/or an height value');
        }

        if (!array_key_exists('quality', $options)) {
            $options['quality'] = 100;
        }

        switch($mode) {
            case self::RESIZE_MODE_OUTBOUND:
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            break;

            case self::RESIZE_MODE_INSET:
                $mode = ImageInterface::THUMBNAIL_INSET;
            break;

            default:
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        }
        
        $image = $this->imagine->load($fromFile->getContent());
        $output = $image
            ->thumbnail(new Box($width, $height), $mode)
            ->get(ExtensionGuesser::guess($media->getContentType()), $options);

        $toFile->setContent($output);
    }


}