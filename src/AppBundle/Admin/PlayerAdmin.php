<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PlayerAdmin extends AbstractAdmin
{


    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'updated',
    );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->add('name', 'text', array('label' => 'Name'))
            ->add('team', 'sonata_type_model_list')
            ->add('position', 'sonata_type_model_list')
            ->add('ek_preis', 'money', array('label' => 'Einkaufspreis'))
            ->add('user', 'sonata_type_model_list', array('label' => 'Teilnehmer'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'name',
                'doctrine_orm_callback',
                [
                    'show_filter' => true,
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }

                        $queryBuilder
                            ->andWhere($alias . '.name LIKE :value')
                            ->setParameter('value', '%'.$value['value'].'%')
                        ;

                        return true;
                    },
                ]
            )
            ->add('team', null, array(
                'show_filter' => true,
            ))
            ->add('position', null, array(
                'show_filter' => true,
            ))
            ->add('user', null, array(
                'show_filter' => true,
            ))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add(
                'team',
                null,
                array(
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'team')),
                )
            )
            ->add('position', null, array('show_filter' => true) )
            ->add('punkte', null, array('show_filter' => true) )
            ->add(
                'user',
                null,
                array(
                    'show_filter' => true,
                    'editable' => true,
                    'label' => 'Käufer'
                )
            )
            ->add(
                'ekPreis',
                'currency',
                array(
                    'currency' => 'EUR',
                    'label' => 'Einkaufspreis',
                    'sortable' => true,
                )
            )
            ->add(
                'vkPreis',
                'currency',
                array(
                    'currency' => 'EUR',
                    'label' => 'Listenpreis',
                    'sortable' => true,
                )
            )
        ;
    }

}