<?php
/**
 * Durst - project - StringToFileTrasformer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 11:44
 */

namespace Pyz\Zed\DriverApp\Communication\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $uploadPath;

    /**
     * StringToFileTransformer constructor.
     * @param string $uploadPath
     */
    public function __construct(string $uploadPath)
    {
        $this->uploadPath = $uploadPath;
    }

    /**
     * @param mixed $fileName
     * @return mixed|File
     */
    public function transform($fileName)
    {
        if($fileName === null || $fileName === ''){
            return '';
        }
        return new File($this->uploadPath . $fileName);
    }

    /**
     * This function does not actually reverse transform the field value, because we need the uploaded
     * file as File-object in the constraint validator @see LogoFileMimeTypeConstraintValidator
     *
     * @param mixed $file
     * @return mixed|string
     */
    public function reverseTransform($file)
    {
        return $file;
    }
}