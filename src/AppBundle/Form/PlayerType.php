<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{

    protected $statics;

    public function __construct($statics = array()) {
        $this->statics = $statics;
    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $vereine = array();
        foreach($this->statics["vereine"] as $verein) {
            $vereine[$verein] = $verein;
        }

        $positionen = array();
        foreach($this->statics["positionen"] as $position) {
            $positionen[$position] = $position;
        }

        $players = array();
        foreach($this->statics["spieler"] as $player) {
            $key = $player;
            if($key === "- alle -") {
                $key = "";
            }
            $players[$key] = $player;
        }


        $builder
            ->add('name')
            ->add('verein', ChoiceType::class, array(
                    'choices'   => $vereine
                )
            )
            ->add('position', ChoiceType::class, array(
                    'choices'   => $positionen
                )
            )
            ->add('vkPreis', MoneyType::class)
            ->add('ekPreis', MoneyType::class)
            ->add('kaeufer', ChoiceType::class, array(
                'choices'   => $players,
                'required'  => false,
                )
            )
            ->add('note', NumberType::class)
            ->add('punkte', NumberType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Player'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'soc_player';
    }
}
