<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtPermissionsBundle\Form;

use Akeneo\Pim\Enrichment\Bundle\Form\Type\CategoryType as BaseCategoryType;
use Akeneo\UserManagement\Component\Model\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends BaseCategoryType
{
    /**
     * {@inheritdoc}
     *
     * * this can be extended to include further fields *
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->addAccessField($builder, 'viewAccess', 'Allowed to view products');

        $this->addAccessField($builder, 'editAccess', 'Allowed to edit products');

        $this->addAccessField($builder, 'ownAccess', 'Allowed to own products');
    }

    private function addAccessField(FormBuilderInterface $builder, string $name, string $label): void
    {
        $builder->add($name, EntityType::class, [
            'class'        => Group::class,
            'choice_label' => function (Group $group) {
                return $group ? $group->getName() : '';
            },
            'label'    => $label,
            'help'     => 'Attention! The changes introduced cause no effect. This is a draft version of functionality',
            'expanded' => false,
            'multiple' => true,
            'required' => false,
        ]);
    }
}