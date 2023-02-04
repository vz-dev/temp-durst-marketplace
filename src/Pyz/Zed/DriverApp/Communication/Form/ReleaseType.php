<?php
/**
 * Durst - project - ReleaseType.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 11:36
 */

namespace Pyz\Zed\DriverApp\Communication\Form;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Pyz\Zed\DriverApp\Communication\Form\DataTransformer\StringToFileTransformer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReleaseType extends AbstractType
{
    public const OPTION_UPLOAD_FILE_PATH = 'OPTION_UPLOAD_FILE_PATH';

    protected const FIELD_SAVE = 'FIELD_SAVE';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $this
            ->addIdField($builder)
            ->addVersionField($builder)
            ->addApkField($builder, $options)
            ->addPatchNotesField($builder)
            ->addSaveButton($builder);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => DriverAppReleaseTransfer::class,
            ]);

        $resolver
            ->setRequired([
                self::OPTION_UPLOAD_FILE_PATH,
            ]);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return ReleaseType
     */
    protected function addIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(DriverAppReleaseTransfer::ID_DRIVER_APP_RELEASE, HiddenType::class, [
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return ReleaseType
     */
    protected function addVersionField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(DriverAppReleaseTransfer::VERSION, TextType::class, [
                'required' => true,
                'label' => 'Version',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return ReleaseType
     */
    protected function addPatchNotesField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(DriverAppReleaseTransfer::PATCH_NOTES, TextareaType::class, [
                'required' => false,
                'label' => 'Patch Notes',
                'constraints' => [
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return ReleaseType
     */
    protected function addApkField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(DriverAppReleaseTransfer::APK_FILE_PATH, FileType::class, [
                'required' => true,
                'label' => 'APK Datei',
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'disallowEmptyMessage' => 'Leere Dateien können nicht hochgeladen werden',
                        'mimeTypes' => [
                                'application/vnd.android.package-archive',
                                'application/zip',
                                'application/java-archive',
                            ],
                    ]),
                ],
            ]);

        $builder
            ->get(DriverAppReleaseTransfer::APK_FILE_PATH)
            ->addModelTransformer(new StringToFileTransformer($options[self::OPTION_UPLOAD_FILE_PATH]));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return ReleaseType
     */
    protected function addSaveButton(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_SAVE, SubmitType::class, [
                'label' => 'Speichern',
            ]);

        return $this;
    }
}
