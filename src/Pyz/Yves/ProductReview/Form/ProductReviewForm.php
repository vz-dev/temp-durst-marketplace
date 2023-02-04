<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\ProductReview\Form;

use Generated\Shared\Transfer\ProductReviewRequestTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * @method \Pyz\Yves\ProductReview\ProductReviewFactory getFactory()
 * @method \Spryker\Client\ProductReview\ProductReviewClientInterface getClient()
 */
class ProductReviewForm extends AbstractType
{
    const FIELD_RATING = ProductReviewRequestTransfer::RATING;
    const FIELD_SUMMARY = ProductReviewRequestTransfer::SUMMARY;
    const FIELD_DESCRIPTION = ProductReviewRequestTransfer::DESCRIPTION;
    const FIELD_NICKNAME = ProductReviewRequestTransfer::NICKNAME;
    const FIELD_PRODUCT = ProductReviewRequestTransfer::ID_PRODUCT_ABSTRACT;

    const UNSELECTED_RATING = -1;
    const MINIMUM_RATING = 1;

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'productReviewForm';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addRatingField($builder)
            ->addSummaryField($builder)
            ->addDescriptionField($builder)
            ->addNicknameField($builder)
            ->addProductField($builder);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function addRatingField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_RATING,
            ChoiceType::class,
            [
                'choices' => array_flip($this->getRatingFieldChoices()),
                'choices_as_values' => true,
                'label' => 'product_review.submit.rating',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => static::MINIMUM_RATING]),
                    new LessThanOrEqual(['value' => $this->getFactory()->getProductReviewClient()->getMaximumRating()]),
                ],
            ]
        );

        return $this;
    }

    /**
     * Returns a sequence between predefined minimum and maximum as an array with a leading "unselected" element
     * - keys match values
     *
     * @see ProductReviewForm::UNSELECTED_RATING
     * @see ProductReviewForm::MINIMUM_RATING
     * @see ProductReviewClientInterface::getMaximumRating()
     *
     * Example
     *  [-1 => 'none', 1 => 1, 2 => 2]
     *
     * @return array
     */
    protected function getRatingFieldChoices()
    {
        $unselectedChoice = [static::UNSELECTED_RATING => 'product_review.submit.rating.none'];
        $choices = range(static::MINIMUM_RATING, $this->getClient()->getMaximumRating());
        $choices = $unselectedChoice + array_combine($choices, $choices);

        return $choices;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSummaryField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_SUMMARY,
            TextType::class,
            [
                'label' => 'product_review.submit.summary',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 1]),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDescriptionField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_DESCRIPTION,
            TextareaType::class,
            [
                'label' => 'product_review.submit.description',
                'attr' => [
                    'rows' => 5,
                ],
                'required' => true,
                'constraints' => [
                    new Length(['min' => 1]),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNicknameField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_NICKNAME,
            TextType::class,
            [
                'label' => 'product_review.submit.nickname',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 1, 'max' => 255]),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_PRODUCT,
            HiddenType::class,
            [
                'required' => true,
            ]
        );

        return $this;
    }
}
