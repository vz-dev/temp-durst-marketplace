<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\MerchantManagement\Communication\Form\Constraints\RelativeDateFormatConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class AbstractBranchForm extends AbstractType
{
    public const OPTION_SALUTATION_OPTIONS = 'OPTION_SALUTATION_OPTIONS';
    public const OPTION_MERCHANT_OPTIONS = 'OPTION_MERCHANT_OPTIONS';

    public const OPTION_PAYMENT_METHOD_CHOICES = 'paymentMethodChoices';

    protected const DATE_FIELD_INPUT_FORMAT = 'yyyy-MM-dd';

    protected const FIELD_NAME = 'name';
    protected const FIELD_SALUTATION = 'contactPersonSalutationId';
    protected const FIELD_MERCHANT = 'fkMerchant';
    protected const FIELD_CONTACT_PERSON_FIRST_NAME = 'contactPersonPreName';
    protected const FIELD_CONTACT_PERSON_LAST_NAME = 'contactPersonName';
    protected const FIELD_EMAIL = 'email';
    protected const FIELD_PHONE = 'phone';
    protected const FIELD_STREET = 'street';
    protected const FIELD_NUMBER = 'number';
    protected const FIELD_ZIP = 'zip';
    protected const FIELD_CITY = 'city';
    protected const FIELD_TERMS_OF_SERVICE = 'termsOfService';
    protected const FIELD_COMPANY_PROFILE = 'companyProfile';
    protected const FIELD_CODE = 'code';
    protected const FIELD_DEFAULT_MIN_VALUE_FIRST = 'defaultMinValueFirst';
    protected const FIELD_DEFAULT_MIN_VALUE_FOLLOWING = 'defaultMinValueFollowing';
    protected const FIELD_DEFAULT_DELIVERY_COSTS = 'defaultDeliveryCosts';
    protected const FIELD_GLN = 'gln';
    protected const FIELD_DURST_GLN = 'durstGln';
    protected const FIELD_CORPORATE_NAME = 'corporateName';
    protected const FIELD_SUMUP_AFFILIATE_KEY = 'sumupAffiliateKey';
    protected const FIELD_EDI_ENDPOINT_URL = 'ediEndpointUrl';
    protected const FIELD_EDI_DEPOSIT_ENDPOINT_URL = 'ediDepositEndpointUrl';
    protected const FIELD_ACCESS_TOKEN = 'accessToken';
    protected const FIELD_BASIC_AUTH_USERNAME = 'basicAuthUsername';
    protected const FIELD_BASIC_AUTH_PASSWORD = 'basicAuthPassword';
    protected const FIELD_AUTO_EDI_EXPORT = 'autoEdiExport';
    protected const FIELD_EDI_EXPORT_VERSION = 'ediExportVersion';
    protected const FIELD_EDI_EXCLUDE_MISSING_ITEM_RETURNS = 'ediExcludeMissingItemReturns';
    protected const FIELD_WAREHOUSE_LAT = 'warehouseLat';
    protected const FIELD_WAREHOUSE_LNG = 'warehouseLng';
    protected const FIELD_HEIDELPAY_PRIVATE_KEY = 'heidelpayPrivateKey';
    protected const FIELD_HEIDELPAY_PUBLIC_KEY = 'heidelpayPublicKey';
    protected const FIELD_BILLING_COMPANY = 'billingCompany';
    protected const FIELD_BILLING_STREET = 'billingStreet';
    protected const FIELD_BILLING_NUMBER = 'billingNumber';
    protected const FIELD_BILLING_ZIP = 'billingZip';
    protected const FIELD_BILLING_CITY = 'billingCity';
    protected const FIELD_SALES_TAX_ID = 'salesTaxId';
    protected const FIELD_PLACE_JURISDICTION = 'placeJurisdiction';
    protected const FIELD_ECO_CONTROL_NUMBER = 'ecoControlNumber';
    protected const FIELD_PERSON_RESPONSIBLE = 'personResponsible';
    protected const FIELD_BILLING_EMAIL = 'billingEmail';
    protected const FIELD_BILLING_CYCLE = 'billingCycle';
    protected const FIELD_BILLING_START_DATE = 'billingStartDate';
    protected const FIELD_BILLING_END_OF_MONTH = 'billingEndOfMonth';
    protected const FIELD_PAYMENT_METHOD = 'paymentMethodIds';
    protected const FIELD_PAYMENT_METHOD_B2C = 'b2cPaymentMethodIds';
    protected const FIELD_PAYMENT_METHOD_B2B = 'b2bPaymentMethodIds';
    protected const FIELD_EXPORT_ACCOUNT = 'exportAccount';
    protected const FIELD_EXPORT_CONTRA_ACCOUNT = 'exportContraAccount';
    protected const FIELD_EXPORT_CSV_ENABLED = 'exportCsvEnabled';
    protected const FIELD_ORDER_ON_TIMESLOT = 'orderOnTimeslot';
    protected const FIELD_OFFERS_DEPOSIT_PICKUP = 'offersDepositPickup';
    protected const FIELD_DATA_RETENTION_DAYS = 'dataRetentionDays';
    protected const FIELD_BILLING_BRANCH_INFORMATION = 'billingBranchInformation';

    protected const LABEL_EDI_ENDPOINT_URL = 'EDI Endpoint URL für Bestellungen';
    protected const LABEL_EDI_DEPOSIT_ENDPOINT_URL = 'EDI Endpoint URL für Leergut/Retouren';
    protected const LABEL_ACCESS_TOKEN = 'Access Token';
    protected const LABEL_BASIC_AUTH_USERNAME = 'Benutzername (EDI)';
    protected const LABEL_BASIC_AUTH_PASSWORD = 'Kennwort (EDI)';
    protected const LABEL_AUTO_EDI_EXPORT = 'Autom. Export Leergut EDI';
    protected const LABEL_EDI_EXPORT_VERSION = 'Genutzte Version des EDI-Exports';
    protected const LABEL_EDI_EXCLUDE_MISSING_ITEM_RETURNS = 'Als fehlend markiertes Vollgut aus Retouren-Export ausschließen';
    protected const LABEL_WAREHOUSE_LAT = 'Lager Lat.';
    protected const LABEL_WAREHOUSE_LNG = 'Lager Lng.';
    protected const LABEL_HEIDELPAY_PRIVATE_KEY = 'Heidelpay Private Key';
    protected const LABEL_HEIDELPAY_PUBLIC_KEY = 'Heidelpay Public Key';
    protected const LABEL_BILLING_COMPANY = 'Firmenname';
    protected const LABEL_BILLING_STREET = 'Strasse';
    protected const LABEL_BILLING_NUMBER = 'Hausnummer';
    protected const LABEL_BILLING_ZIP = 'PLZ';
    protected const LABEL_BILLING_CITY = 'Stadt';
    protected const LABEL_SALES_TAX_ID = 'Umsatzsteuer-ID';
    protected const LABEL_PLACE_JURISDICTION = 'Gerichtsstand';
    protected const LABEL_ECO_CONTROL_NUMBER = 'Öko-Kontrollnummer';
    protected const LABEL_PERSON_RESPONSIBLE = 'Verantwortliche Person(en)';
    protected const LABEL_BILLING_EMAIL = 'Email Buchhaltung';
    protected const LABEL_BILLING_CYCLE = 'Rechnungs-Zyklus';
    protected const LABEL_BILLING_START_DATE = 'Rechnungs-Start-Datum';
    protected const LABEL_BILLING_END_OF_MONTH = 'Rechnung zum Ende des Monats?';
    protected const LABEL_PAYMENT_METHODS_B2B = 'Zahlungsmethoden B2B';
    protected const LABEL_PAYMENT_METHODS_B2C = 'Zahlungsmethoden B2C';
    protected const LABEL_EXPORT_ACCOUNT = 'Konto (Export CSV)';
    protected const LABEL_EXPORT_CONTRA_ACCOUNT = 'Gegenkonto (Export CSV)';
    protected const LABEL_EXPORT_CSV_ENABLED = 'DATEV CSV Export';
    protected const LABEL_ORDER_ON_TIMESLOT = 'Bestellungen auf Zeitfenster';
    protected const LABEL_OFFERS_DEPOSIT_PICKUP = 'Leergut-Abholung anbieten';
    protected const LABEL_DATA_RETENTION_DAYS = 'Driver App Datenspeicherungszeitraum in Tagen';
    protected const LABEL_BILLING_BRANCH_INFORMATION = 'Filialinformation';

    protected const MESSAGE_INVALID_URL = 'Bitte geben Sie eine valide Url ein.';

    protected const BUTTON_SUBMIT = 'submit';

    protected const DISABLED_PAYMENT_METHODS_FOR_B2B = ['SEPA-Lastschrift garantiert'];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addNameField($builder)
            ->addMerchantField($builder, $options)
            ->addSalutationField($builder, $options)
            ->addContactPersonFirstNameField($builder)
            ->addContactPersonLastNameField($builder)
            ->addEmailField($builder)
            ->addPhoneField($builder)
            ->addStreetField($builder)
            ->addNumberField($builder)
            ->addZipField($builder)
            ->addCityField($builder)
            ->addTermsOfServiceField($builder)
            ->addCompanyProfileField($builder)
            ->addCodeField($builder)
            ->addDefaultMinValueFirstField($builder)
            ->addDefaultMinValueFollowingField($builder)
            ->addDefaultDeliveryCostsField($builder)
            ->addGlnField($builder)
            ->addDurstGlnField($builder)
            ->addCorporateNameField($builder)
            ->addSumupAffiliateKey($builder)
            ->addEdiEndpointUrlField($builder)
            ->addEdiDepositEndpointUrlField($builder)
            ->addAccessTokenField($builder)
            ->addBasicAuthUsernameField($builder)
            ->addBasicAuthPasswordField($builder)
            ->addAutoEdiExportField($builder)
            ->addEdiExcludeMissingItemReturnsField($builder)
            ->addEdiExportVersionField($builder)
            ->addWarehouseLatField($builder)
            ->addWarehouseLngField($builder)
            ->addHeidelpayPrivateKeyField($builder)
            ->addHeidelpayPublicKeyField($builder)
            ->addBillingCompanyField($builder)
            ->addBillingStreetField($builder)
            ->addBillingNumberField($builder)
            ->addBillingZipField($builder)
            ->addBillingCityField($builder)
            ->addSalesTaxIdField($builder)
            ->addPlaceJurisdictionField($builder)
            ->addEcoControlNumberField($builder)
            ->addPersonResponsibleField($builder)
            ->addBillingEmailField($builder)
            ->addBillingStartDateField($builder)
            ->addBillingCycleField($builder)
            ->addBillingEndOfMonthField($builder)
            ->addOrderOnTimeslotField($builder)
            ->addOffersDepositPickupField($builder)
            ->addDataRetentionDaysField($builder)
            ->addBillingBranchInformationField($builder)
            ->addSubmitButton($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addMerchantField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(self::FIELD_MERCHANT, ChoiceType::class, [
                'label' => 'Händler',
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => $options[self::OPTION_MERCHANT_OPTIONS],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addSalutationField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(self::FIELD_SALUTATION, ChoiceType::class, [
                'label' => 'Anrede',
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => $options[self::OPTION_SALUTATION_OPTIONS],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonFirstNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_CONTACT_PERSON_FIRST_NAME, TextType::class, [
                'label' => 'Vorname',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonLastNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_CONTACT_PERSON_LAST_NAME, TextType::class, [
                'label' => 'Name',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_EMAIL, EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPhoneField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_PHONE, TextType::class, [
                'label' => 'Telefon',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addStreetField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_STREET, TextType::class, [
                'label' => 'Straße',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNumberField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_NUMBER, TextType::class, [
                'label' => 'Hausnummer',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addZipField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_ZIP, TextType::class, [
                'label' => 'PLZ',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCityField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_CITY, TextType::class, [
                'label' => 'Ort',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addTermsOfServiceField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_TERMS_OF_SERVICE, TextareaType::class, [
                'label' => 'AGBs',
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCompanyProfileField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_COMPANY_PROFILE, TextareaType::class, [
                'label' => 'Unternehmensprofil',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $additionalOptions
     *
     * @return $this
     */
    protected function addCodeField(FormBuilderInterface $builder, array $additionalOptions = []): AbstractBranchForm
    {
        $options = array_merge([
            'label' => 'Branch-Code',
            'required' => false
        ], $additionalOptions);

        $builder->add(self::FIELD_CODE, TextType::class, $options);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDefaultMinValueFirstField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_DEFAULT_MIN_VALUE_FIRST, MoneyType::class, [
                'label' => 'Mindestbestellwert (erste Bestellung)',
                'divisor' => 100,
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDefaultMinValueFollowingField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_DEFAULT_MIN_VALUE_FOLLOWING, MoneyType::class, [
                'label' => 'Mindestbestellwert (folgende Bestellung)',
                'divisor' => 100,
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDefaultDeliveryCostsField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_DEFAULT_DELIVERY_COSTS, MoneyType::class, [
                'label' => 'Liefergebühr',
                'divisor' => 100,
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addGlnField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_GLN, TextType::class, [
                'label' => 'GLN',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDurstGlnField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_DURST_GLN, TextType::class, [
                'label' => 'Durst-GLN',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCorporateNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_CORPORATE_NAME, TextType::class, [
                'label' => 'Firmierung',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSumupAffiliateKey(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_SUMUP_AFFILIATE_KEY, TextType::class, [
                'label' => 'Sumup Affiliate Key',
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEdiEndpointUrlField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_EDI_ENDPOINT_URL, TextType::class, [
                'label' => self::LABEL_EDI_ENDPOINT_URL,
                'required' => false,
                'constraints' => [
                    new Url(['message' => self::MESSAGE_INVALID_URL]),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEdiDepositEndpointUrlField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_EDI_DEPOSIT_ENDPOINT_URL, TextType::class, [
                'label' => self::LABEL_EDI_DEPOSIT_ENDPOINT_URL,
                'required' => false,
                'constraints' => [
                    new Url(['message' => self::MESSAGE_INVALID_URL]),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAccessTokenField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_ACCESS_TOKEN, TextType::class, [
                'label' => self::LABEL_ACCESS_TOKEN,
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBasicAuthUsernameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_BASIC_AUTH_USERNAME, TextType::class, [
                'label' => self::LABEL_BASIC_AUTH_USERNAME,
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBasicAuthPasswordField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(self::FIELD_BASIC_AUTH_PASSWORD, TextType::class, [
                'label' => self::LABEL_BASIC_AUTH_PASSWORD,
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAutoEdiExportField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_AUTO_EDI_EXPORT,
                CheckboxType::class,
                [
                    'label' => self::LABEL_AUTO_EDI_EXPORT,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEdiExcludeMissingItemReturnsField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_EDI_EXCLUDE_MISSING_ITEM_RETURNS,
                CheckboxType::class,
                [
                    'label' => self::LABEL_EDI_EXCLUDE_MISSING_ITEM_RETURNS,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEdiExportVersionField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_EDI_EXPORT_VERSION,
                ChoiceType::class,
                [
                    'label' => self::LABEL_EDI_EXPORT_VERSION,
                    'required' => false,
                    'choices' => EdifactConstants::EDIFACT_EXPORT_VERSION_CHOICES,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addWarehouseLatField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_WAREHOUSE_LAT, NumberType::class, [
                'label' => static::LABEL_WAREHOUSE_LAT,
                'scale' => 7,
                'constraints' => [
                ],
                'required' => true,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addWarehouseLngField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_WAREHOUSE_LNG, NumberType::class, [
                'label' => static::LABEL_WAREHOUSE_LNG,
                'scale' => 7,
                'constraints' => [
                ],
                'required' => true,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addHeidelpayPrivateKeyField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_HEIDELPAY_PRIVATE_KEY,
                TextType::class,
                [
                    'label' => static::LABEL_HEIDELPAY_PRIVATE_KEY,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addHeidelpayPublicKeyField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_HEIDELPAY_PUBLIC_KEY,
                TextType::class,
                [
                    'label' => static::LABEL_HEIDELPAY_PUBLIC_KEY,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingCompanyField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_COMPANY,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_COMPANY,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingStreetField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_STREET,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_STREET,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingNumberField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_NUMBER,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_NUMBER,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingZipField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_ZIP,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_ZIP,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingCityField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_CITY,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_CITY,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSalesTaxIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_SALES_TAX_ID,
                TextType::class,
                [
                    'label' => static::LABEL_SALES_TAX_ID,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPlaceJurisdictionField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_PLACE_JURISDICTION,
                TextType::class,
                [
                    'label' => static::LABEL_PLACE_JURISDICTION,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEcoControlNumberField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_ECO_CONTROL_NUMBER,
                TextType::class,
                [
                    'label' => static::LABEL_ECO_CONTROL_NUMBER,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPersonResponsibleField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_PERSON_RESPONSIBLE,
                TextType::class,
                [
                    'label' => static::LABEL_PERSON_RESPONSIBLE,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingEmailField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_EMAIL,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_EMAIL,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingStartDateField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_START_DATE,
                DateType::class,
                [
                    'input' => 'string',
                    'widget' => 'single_text',
                    'format' => static::DATE_FIELD_INPUT_FORMAT,
                    'label' => static::LABEL_BILLING_START_DATE,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingCycleField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_CYCLE,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_CYCLE,
                    'required' => false,
                    'constraints' => [
                        new RelativeDateFormatConstraint(),
                    ],
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBillingEndOfMonthField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_END_OF_MONTH,
                CheckboxType::class,
                [
                    'label' => static::LABEL_BILLING_END_OF_MONTH,
                    'required' => false,
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addOrderOnTimeslotField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_ORDER_ON_TIMESLOT,
                CheckboxType::class,
                [
                    'label' => static::LABEL_ORDER_ON_TIMESLOT,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addOffersDepositPickupField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_OFFERS_DEPOSIT_PICKUP,
                CheckboxType::class,
                [
                    'label' => static::LABEL_OFFERS_DEPOSIT_PICKUP,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addBillingBranchInformationField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_BILLING_BRANCH_INFORMATION,
                TextType::class,
                [
                    'label' => static::LABEL_BILLING_BRANCH_INFORMATION,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder): AbstractBranchForm
    {
        $builder
            ->add(self::BUTTON_SUBMIT, SubmitType::class, [
                'label' => static::LABEL_BUTTON_SUBMIT,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addDataRetentionDaysField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_DATA_RETENTION_DAYS,
                NumberType::class,
                [
                    'label' => static::LABEL_DATA_RETENTION_DAYS,
                    'required' => false
                ]
            );

        return $this;
    }
}
