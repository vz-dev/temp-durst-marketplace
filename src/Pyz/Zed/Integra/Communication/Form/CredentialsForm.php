<?php
/**
 * Durst - project - CredentialsForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 09:46
 */

namespace Pyz\Zed\Integra\Communication\Form;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredentialsForm extends AbstractType
{
    public const OPTION_BRANCHES = 'OPTION_BRANCHES';

    public const BTN_SAVE = 'btnSave';

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([static::OPTION_BRANCHES]);
        $resolver->setDefault('data_class', IntegraCredentialsTransfer::class);
    }

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
            ->addUseIntegraField($builder)
            ->addBranchField($builder, $options)
            ->addIpAddressField($builder)
            ->addSoapUserField($builder)
            ->addSoapPasswordField($builder)
            ->addSoapMandantField($builder)
            ->addSoapBetrStrField($builder)
            ->addFtpHost($builder)
            ->addFtpUser($builder)
            ->addFtpPassword($builder)
            ->addOpenOrdersCsvPathField($builder)
            ->addClosedOrdersCsvPathField($builder)
            ->addSaveButton($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addBranchField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::FK_BRANCH, ChoiceType::class, [
                'label' => 'Branch',
                'required' => true,
                'choices' => $options[static::OPTION_BRANCHES],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addUseIntegraField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::USE_INTEGRA, CheckboxType::class, [
                'label' => 'Orga-Soft Integra verwenden?',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIpAddressField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::INTEGRA_IP_ADDRESS, TextType::class, [
                'label' => 'IP:Port Integra-Server',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSoapUserField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::SOAP_AUTH_USER, TextType::class, [
                'label' => 'SOAP User',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSoapPasswordField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::SOAP_AUTH_PASSWORD, PasswordType::class, [
                'label' => 'SOAP Passwort',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSoapMandantField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::SOAP_AUTH_MANDANT, IntegerType::class, [
                'label' => 'SOAP Mandant',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSoapBetrStrField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::SOAP_AUTH_BETR_STR, IntegerType::class, [
                'label' => 'SOAP Betriebsstätte',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFtpHost(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::FTP_HOST, TextType::class, [
                'label' => 'Host',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFtpUser(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::FTP_USER, TextType::class, [
                'label' => 'Benutzer',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFtpPassword(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::FTP_PASSWORD, PasswordType::class, [
                'label' => 'Passwort',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addOpenOrdersCsvPathField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::OPEN_ORDER_CSV_PATH, TextType::class, [
                'label' => 'Pfad Vollgut-CSV-Export',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addClosedOrdersCsvPathField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(IntegraCredentialsTransfer::CLOSED_ORDER_CSV_PATH, TextType::class, [
                'label' => 'Pfad Leergut/Retouren-CSV-Export',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSaveButton(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::BTN_SAVE, SubmitType::class, [
                'label' => 'Speichern',
            ]);

        return $this;
    }
}
