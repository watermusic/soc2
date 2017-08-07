<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ScoreAdmin extends AbstractAdmin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('matchday', 'choice', array('label' => 'Spieltag', 'choices' => $this->getChoices()))
            ->add('player', 'sonata_type_model_list', array('label' => 'Teilnehmer'))
            ->add('score', 'text', array('label' => 'Punkte'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('matchday')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('matchday')
            ->add('player')
            ->add('score')
        ;
    }

    private function getChoices()
    {

        $choices = array();
        foreach (range(1, 34) as $matchday) {
            $choices[$matchday] = $matchday;
        }

        return $choices;
    }

}