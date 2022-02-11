<?php

namespace App\Service;

use App\Repository\SizeRepository;

class FormatConversion
{
    private $sizeRepository;

    public function __construct(SizeRepository $sizeRepository)
    {
        $this->sizeRepository = $sizeRepository;
    }

    const HEIGHTS = [22, 24, 27, 33, 35, 41, 46, 55, 61, 65, 73, 81, 92, 100, 116, 130, 146, 162, 195];
    const WIDTHS = [12, 14, 16, 19, 22, 24, 27, 33, 38, 46, 50, 54, 60, 65, 73, 81, 89, 97, 114, 130];
    const MEASUREMENTS = [12, 14, 16, 19, 22, 24, 27, 33, 35, 38, 41, 46, 50, 54, 55, 60, 61, 65, 73, 81, 89, 92, 97, 100, 114, 116, 130, 146, 162, 195];
    const FIGURE = [
        1 => [
            'height' => 22,
            'width' => 16,
        ],
        2 => [
            'height' => 24,
            'width' => 19,
        ],
        3 => [
            'height' => 27,
            'width' => 22,
        ],
        4 => [
            'height' => 33,
            'width' => 24,
        ],
        5 => [
            'height' => 35,
            'width' => 27,
        ],
        6 => [
            'height' => 41,
            'width' => 33,
        ],
        8 => [
            'height' => 46,
            'width' => 38,
        ],
        10 => [
            'height' => 55,
            'width' => 46,
        ],
        12 => [
            'height' => 61,
            'width' => 50,
        ],
        15 => [
            'height' => 65,
            'width' => 54,
        ],
        20 => [
            'height' => 73,
            'width' => 60,
        ],
        25 => [
            'height' => 81,
            'width' => 65,
        ],
        30 => [
            'height' => 92,
            'width' => 73,
        ],
        40 => [
            'height' => 100,
            'width' => 81,
        ],
        50 => [
            'height' => 116,
            'width' => 89,
        ],
        60 => [
            'height' => 130,
            'width' => 97,
        ],
        80 => [
            'height' => 146,
            'width' => 114,
        ],
        100 => [
            'height' => 162,
            'width' => 130,
        ],
        120 => [
            'height' => 195,
            'width' => 130,
        ],
    ];
    const PAYSAGE = [
        1 => [
            'height' => 22,
            'width' => 14,
        ],
        2 => [
            'height' => 24,
            'width' => 16,
        ],
        3 => [
            'height' => 27,
            'width' => 19,
        ],
        4 => [
            'height' => 33,
            'width' => 22,
        ],
        5 => [
            'height' => 35,
            'width' => 24,
        ],
        6 => [
            'height' => 41,
            'width' => 27,
        ],
        8 => [
            'height' => 46,
            'width' => 33,
        ],
        10 => [
            'height' => 55,
            'width' => 38,
        ],
        12 => [
            'height' => 61,
            'width' => 46,
        ],
        15 => [
            'height' => 65,
            'width' => 50,
        ],
        20 => [
            'height' => 73,
            'width' => 54,
        ],
        25 => [
            'height' => 81,
            'width' => 60,
        ],
        30 => [
            'height' => 92,
            'width' => 65,
        ],
        40 => [
            'height' => 100,
            'width' => 73,
        ],
        50 => [
            'height' => 116,
            'width' => 81,
        ],
        60 => [
            'height' => 130,
            'width' => 89,
        ],
        80 => [
            'height' => 146,
            'width' => 97,
        ],
        100 => [
            'height' => 162,
            'width' => 114,
        ],
        120 => [
            'height' => 195,
            'width' => 114,
        ],
    ];
    const MARINE =[
        1 => [
            'height' => 22,
            'width' => 12,
        ],
        2 => [
            'height' => 24,
            'width' => 14,
        ],
        3 => [
            'height' => 27,
            'width' => 16,
        ],
        4 => [
            'height' => 33,
            'width' => 19,
        ],
        5 => [
            'height' => 35,
            'width' => 22,
        ],
        6 => [
            'height' => 41,
            'width' => 24,
        ],
        8 => [
            'height' => 46,
            'width' => 27,
        ],
        10 => [
            'height' => 55,
            'width' => 33,
        ],
        12 => [
            'height' => 61,
            'width' => 38,
        ],
        15 => [
            'height' => 65,
            'width' => 46,
        ],
        20 => [
            'height' => 73,
            'width' => 50,
        ],
        25 => [
            'height' => 81,
            'width' => 54,
        ],
        30 => [
            'height' => 92,
            'width' => 60,
        ],
        40 => [
            'height' => 100,
            'width' => 65,
        ],
        50 => [
            'height' => 116,
            'width' => 73,
        ],
        60 => [
            'height' => 130,
            'width' => 81,
        ],
        80 => [
            'height' => 146,
            'width' => 89,
        ],
        100 => [
            'height' => 162,
            'width' => 97,
        ],
        120 => [
            'height' => 195,
            'width' => 97,
        ],
    ];
    private $formatType = ['F' => self::FIGURE, 'P' => self::PAYSAGE, 'M' => self::MARINE];

    /**
     * Get the measurements (height/width) depending on the format given.
     * For example if the format is 30F then the measurements will be 92*73 (h*w)
     *
     * @param string $format    Format of the painting
     * @return array ['height' => height value, 'width' => width value]
     */
    public function getWidthHeightFromFormat(string $format): array
    {
        // Get the letter of the format
        $sizeLetter = substr($format, strlen($format) - 1, 1);

        // Get the number of the format
        $sizeNumber = substr($format, 0, strlen($format) - 1);

        // if ('Sans format' === $format) {
        //     return [];
        // }

        $reference = $this->formatType[$sizeLetter];

        $sizes = $reference[$sizeNumber];

        return $sizes;
    }

    /**
     * Convert the height and width into the automated format/size
     * For example if the height and width are 81 and 54, it will give the format 25M
     * Return false if none value is associated to the height and width given
     *
     * @param integer $height   Height of the painting
     * @param integer $width    Width of the painting
     * @return array|false      Returns false if the format doesn't exist, otherwise
     *                          returns the orientation and the size/format
     */
    public function getFormatFromtWidthHeight(int $height, int $width): array|false
    {
        $heightExists = in_array($height, self::MEASUREMENTS);
        $widthExists = in_array($width, self::MEASUREMENTS);

        if ($heightExists && $widthExists) {

            foreach (self::FIGURE as $key => $value) {
                if ($value['height'] == $height && $value['width'] == $width) {
                    return ['Orientation' => 'V', 'format' => $key.'F'];
                } else if ($value['height'] == $width && $value['width'] == $height) {
                    return ['Orientation' => 'H', 'format' => $key.'F'];
                }
            }

            foreach (self::PAYSAGE as $key => $value) {
                if ($value['height'] == $height && $value['width'] == $width) {
                    return ['Orientation' => 'V', 'format' => $key.'P'];
                } else if ($value['height'] == $width && $value['width'] == $height) {
                    return ['Orientation' => 'H', 'format' => $key.'P'];
                }
            }

            foreach (self::MARINE as $key => $value) {
                if ($value['height'] == $height && $value['width'] == $width) {
                    return ['Orientation' => 'V', 'format' => $key.'M'];
                } else if ($value['height'] == $width && $value['width'] == $height) {
                    return ['Orientation' => 'H', 'format' => $key.'M'];
                }
            }

            return false;
        }

        return false;
    }

    /**
     * Check if the Format given and the Width/Height also given are corresponding
     * or if there's a mistake between both
     *
     * @param string $size      The size/format of the painting to compare to
     * @param integer $height   The height of the painting to compare to
     * @param integer $width    The width of the painting to compare to
     * @return boolean          Returns true if the comparison is correct, false if it's not matching
     */
    public function checkWidthHeightAndFormat(string $size, int $height, int $width): bool
    {
        if ('Sans format' === $size) {
            return true;
        }

        $sizes = $this->getWidthHeightFromFormat($size);

        if ($sizes['height'] == $height && $sizes['width'] == $width) {
            return true;
        } else if ($sizes['height'] == $width && $sizes['width'] == $height) {
            return true;
        }

        return false;
    }

    /**
     * Check if tge picture if vertical or horizontal. (only pictures) ; 
     * If true the picture is vertical.
     *
     * @param mixed $picture
     * @return boolean true if vertical / false if horizontal
     */
    public function getPictureOrientation($picture)
    {
        $imageWidth = getimagesize($picture)[0];
        $imageHeight = getimagesize($picture)[1];

        if ($imageWidth > $imageHeight) {
            return false;
        }

        return true;
    }

    /**
     * Set the size/format, height and width of a painting
     *
     * @param object $painting The painting before setting informations
     * @return object $painting modified
     */
    public function setSizes(object $painting): object
    {
        $picture = $painting->getPicture();

        $height = $painting->getHeight();
        $width = $painting->getWidth();

        if (null != $painting->getSize() && (null === $height || null === $width)) {
            $sizes = $this->getWidthHeightFromFormat($painting->getSize()->getFormat());

            if ('V' === $picture->getOrientation() || null === $picture->getOrientation()) {
                $painting->setHeight($sizes['height']);
                $painting->setWidth($sizes['width']);    
            } else {
                $painting->setHeight($sizes['width']);
                $painting->setWidth($sizes['height']);    
            }
        }

        if (null === $painting->getSize() && null != $height && null != $width) {
            $format = $this->getFormatFromtWidthHeight($height, $width);
            if (false != $format) {
                $foundSize = $this->sizeRepository->findOneBy(['format' => $format['format']]);     
                $painting->setSize($foundSize);

            }

            if (false === $format) {
                $painting->setSize($this->sizeRepository->find(0));
            }
        }

        if ('H' === $picture->getOrientation() && $height > $width) {
            $painting->setHeight($width);
            $painting->setWidth($height);
        }
        if ('V' === $picture->getOrientation() && $height < $width) {
            $painting->setHeight($width);
            $painting->setWidth($height);
        }

        return $painting;
    }

    /**
     * Check the size/format and width/height of a painting to specify if there's
     * a mistake or not and put a message or not in the description
     *
     * @param object $painting Object painting to check
     * @return object $painting modified
     */
    public function setWarningSizeMessage(object $painting): object
    {
        if (null != $painting->getSize()) {
            $comparison = $this->checkWidthHeightAndFormat($painting->getSize()->getFormat(), $painting->getHeight(), $painting->getWidth());

            if (!$comparison) {
                $painting->setInformation('Attention ! Il y a une différence entre les dimensions et le format mentionné ! '.$painting->getInformation());
            }

            if ($comparison) {
                $checkMessageExists = strstr($painting->getInformation(), 'Attention ! Il y a une différence entre les dimensions et le format mentionné !');
                if (false != $checkMessageExists) {
                    $removeComparison = str_replace('Attention ! Il y a une différence entre les dimensions et le format mentionné !', '', $painting->getInformation());
                    $painting->setInformation('' != $removeComparison ? $removeComparison : null);
                }
            }    
        } else {
            $checkMessageExists = strstr($painting->getInformation(), 'Attention ! Il y a une différence entre les dimensions et le format mentionné !');
            if (false != $checkMessageExists) {
                $removeComparison = str_replace('Attention ! Il y a une différence entre les dimensions et le format mentionné !', '', $painting->getInformation());
                $painting->setInformation('' != $removeComparison ? $removeComparison : null);
            }
        }

        return $painting;
    }
}
