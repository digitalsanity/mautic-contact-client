<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactClientBundle\Form\Type;

// use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\LeadBundle\Model\LeadModel as ContactModel;
use MauticPlugin\MauticContactClientBundle\Constraints\JsonArray;
use MauticPlugin\MauticContactClientBundle\Constraints\JsonObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ContactClientType.
 */
class ContactClientType extends AbstractType
{
    /** @var CorePermissions */
    private $security;

    /** @var ContactModel */
    private $contactModel;

    /**
     * ContactClientType constructor.
     *
     * @param CorePermissions $security
     * @param ContactModel    $contactModel
     */
    public function __construct(
        CorePermissions $security,
        ContactModel $contactModel
    ) {
        $this->security     = $security;
        $this->contactModel = $contactModel;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // CleanFormSubscriber causes JSON payloads containing XML to be purged :(
        // $builder->addEventSubscriber(new CleanFormSubscriber(['website' => 'url']));
        $builder->addEventSubscriber(new FormExitSubscriber('contactclient', $options));

        $builder->add(
            'name',
            'text',
            [
                'label'      => 'mautic.core.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'description',
            'textarea',
            [
                'label'      => 'mautic.core.description',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control editor'],
                'required'   => false,
            ]
        );

        $builder->add(
            'api_payload',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.api_payload',
                'label_attr'  => ['class' => 'control-label api-payload'],
                'attr'        => [
                    'class' => 'form-control api-payload hide',
                    'rows'  => 12,
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'file_payload',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.file_payload',
                'label_attr'  => ['class' => 'control-label file-payload'],
                'attr'        => [
                    'class' => 'form-control file-payload hide',
                    'rows'  => 12,
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'website',
            'url',
            [
                'label'      => 'mautic.contactclient.form.website',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.contactclient.form.website.tooltip',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'attribution_default',
            'number',
            [
                'label'      => 'mautic.contactclient.form.attribution.default',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-money',
                    'tooltip'  => 'mautic.contactclient.form.attribution.default.tooltip',
                ],
                'required'   => false,
            ]
        );

        $builder->add(
            'attribution_settings',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.attribution.settings',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class' => 'form-control hide',
                    'rows'  => 12,
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'duplicate',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.duplicate',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'rows'    => 12,
                    'tooltip' => 'mautic.contactclient.form.duplicate.tooltip',
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'exclusive',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.exclusive',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'rows'    => 12,
                    'tooltip' => 'mautic.contactclient.form.exclusive.tooltip',
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $exclusiveIgnore = [
            'read_only'  => false,
            'data'       => (bool) $options['data']->getExclusiveIgnore(),
            'label'      => 'mautic.contactclient.form.exclusive_ignore',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'       => 'form-control',
                'empty_value' => false,
                'tooltip'     => 'mautic.contactclient.form.exclusive_ignore.tooltip',
            ],
        ];
        if (!$this->security->isAdmin()) {
            $exclusiveIgnore['disabled']         = true;
            $exclusiveIgnore['attr']['disabled'] = 'disabled';
        }
        $builder->add(
            'exclusive_ignore',
            'yesno_button_group',
            $exclusiveIgnore
        );

        $builder->add(
            'filter',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.filter',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'tooltip' => 'mautic.contactclient.form.filter.tooltip',
                    'rows'    => 12,
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'limits',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.limits',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'rows'    => 12,
                    'tooltip' => 'mautic.contactclient.form.limits.tooltip',
                ],
                'required'    => false,
                'constraints' => [new JsonObject()],
            ]
        );

        $builder->add(
            'limits_queue',
            'yesno_button_group',
            [
                'read_only'  => false,
                'data'       => (bool) $options['data']->getLimitsQueue(),
                'label'      => 'mautic.contactclient.form.limits_queue',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'empty_value' => false,
                    'tooltip'     => 'mautic.contactclient.form.limits_queue.tooltip',
                ],
            ]
        );

        $builder->add(
            'limits_queue_spread',
            'range',
            [
                'required'   => false,
                'data'       => min(7, max(1, (int) $options['data']->getLimitsQueueSpread())),
                'label'      => 'mautic.contactclient.form.limits_queue_spread',
                'label_attr' => [
                    'class' => 'control-label pr-10',
                    'style' => 'display: block;',
                ],
                'attr'       => [
                    'min'     => 1,
                    'max'     => 7,
                    'step'    => 1,
                    'class'   => 'form-control form-control-range',
                    'tooltip' => 'mautic.contactclient.form.limits_queue_spread.tooltip',
                ],
            ]
        );

        $builder->add(
            'schedule_timezone',
            'timezone',
            [
                'label'       => 'mautic.contactclient.form.schedule_timezone',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.contactclient.form.schedule_timezone.tooltip',
                ],
                'multiple'    => false,
                'empty_value' => 'mautic.user.user.form.defaulttimezone',
                'required'    => false,
            ]
        );

        $builder->add(
            'schedule_hours',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.schedule_hours',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'rows'    => 12,
                    'tooltip' => 'mautic.contactclient.form.schedule_hours.tooltip',
                ],
                'required'    => false,
                'constraints' => [new JsonArray()],
            ]
        );

        $builder->add(
            'schedule_queue',
            'yesno_button_group',
            [
                'read_only'  => false,
                'data'       => (bool) $options['data']->getScheduleQueue(),
                'label'      => 'mautic.contactclient.form.schedule_queue',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'empty_value' => false,
                    'tooltip'     => 'mautic.contactclient.form.schedule_queue.tooltip',
                ],
            ]
        );

        $builder->add(
            'schedule_queue_spread',
            'range',
            [
                'required'   => false,
                'data'       => min(7, max(1, (int) $options['data']->getScheduleQueueSpread())),
                'label'      => 'mautic.contactclient.form.schedule_queue_spread',
                'label_attr' => [
                    'class' => 'control-label pr-10',
                    'style' => 'display: block;',
                ],
                'attr'       => [
                    'min'     => 1,
                    'max'     => 7,
                    'step'    => 1,
                    'class'   => 'form-control form-control-range',
                    'tooltip' => 'mautic.contactclient.form.schedule_queue_spread.tooltip',
                ],
            ]
        );

        $builder->add(
            'schedule_exclusions',
            'textarea',
            [
                'label'       => 'mautic.contactclient.form.schedule_exclusions',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control hide',
                    'rows'    => 12,
                    'tooltip' => 'mautic.contactclient.form.schedule_exclusions.tooltip',
                ],
                'required'    => false,
                'constraints' => [new JsonArray()],
            ]
        );

        $builder->add(
            'dnc_checks',
            'choice',
            [
                'choices'    => array_flip($this->contactModel->getPreferenceChannels()),
                'data'       => explode(',', $options['data']->getDncChecks()),
                'multiple'   => true,
                'expanded'   => true,
                'required'   => false,
                'label'      => 'mautic.contactclient.form.dnc_checks',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.contactclient.form.dnc_checks.tooltip',
                ],
            ]
        );

        //add category
        $builder->add(
            'category',
            'category',
            [
                'bundle' => 'plugin:contactclient',
            ]
        );

        if (!empty($options['data']) && $options['data']->getId()) {
            $readonly = !$this->security->isGranted('plugin:contactclient:items:publish');
            $data     = $options['data']->isPublished(false);
        } elseif (!$this->security->isGranted('plugin:contactclient:items:publish')) {
            $readonly = true;
            $data     = false;
        } else {
            $readonly = false;
            $data     = false;
        }

        $builder->add(
            'isPublished',
            'yesno_button_group',
            [
                'read_only' => $readonly,
                'data'      => $data,
            ]
        );

        $builder->add(
            'publishUp',
            'datetime',
            [
                'widget'     => 'single_text',
                'label'      => 'mautic.core.form.publishup',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                ],
                'format'     => 'yyyy-MM-dd HH:mm',
                'required'   => false,
            ]
        );

        $builder->add(
            'publishDown',
            'datetime',
            [
                'widget'     => 'single_text',
                'label'      => 'mautic.core.form.publishdown',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                ],
                'format'     => 'yyyy-MM-dd HH:mm',
                'required'   => false,
            ]
        );

        $builder->add(
            'type',
            'button_group',
            [
                'label'             => 'mautic.contactclient.form.type',
                'label_attr'        => ['class' => 'control-label contactclient-type'],
                'choices'           => [
                    'mautic.contactclient.form.type.api'  => 'api',
                    'mautic.contactclient.form.type.file' => 'file',
                ],
                'choices_as_values' => true,
                'required'          => true,
                'attr'              => [
                    'class'    => 'form-control',
                    'tooltip'  => 'mautic.contactclient.form.type.tooltip',
                    'onchange' => 'Mautic.contactclientTypeChange(this);',
                ],
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }

        $customButtons = [];

        if (!empty($options['update_select'])) {
            $builder->add(
                'buttons',
                'form_buttons',
                [
                    'apply_text'        => false,
                    'pre_extra_buttons' => $customButtons,
                ]
            );
            $builder->add(
                'updateSelect',
                'hidden',
                [
                    'data'   => $options['update_select'],
                    'mapped' => false,
                ]
            );
        } else {
            $builder->add(
                'buttons',
                'form_buttons',
                [
                    'pre_extra_buttons' => $customButtons,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'MauticPlugin\MauticContactClientBundle\Entity\ContactClient',
            ]
        );
        $resolver->setDefined(['update_select']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contactclient';
    }
}
