<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Perform\BaseBundle\Routing\MissingResourceException;

/**
 * Render action buttons and select options.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $crudUrlGenerator;
    protected $registry;
    protected $store;
    protected $request;

    public function __construct(UrlGeneratorInterface $urlGenerator, CrudUrlGeneratorInterface $crudUrlGenerator, ActionRegistry $registry, ConfigStoreInterface $store)
    {
        $this->urlGenerator = $urlGenerator;
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->registry = $registry;
        $this->store = $store;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_action_button', [$this, 'actionButton'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_action_option', [$this, 'actionOption'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_action_buttons_for', [$this, 'buttonsForEntity']),
        ];
    }

    public function setCrudRequest(CrudRequest $request)
    {
        $this->request = $request;
    }

    public function actionButton(\Twig_Environment $twig, ConfiguredAction $action, $crudName, $entity, $context, array $attr = [])
    {
        $label = $action->getLabel($this->request, $entity);

        $attr['data-action'] = json_encode([
            'crudName' => $crudName,
            'ids' => [$entity->getId()],
            'label' => $label,
            'context' => $context,
            'message' => $action->getConfirmationMessage($this->request, $entity),
            'confirm' => $action->isConfirmationRequired(),
            'buttonStyle' => $action->getButtonStyle(),
            'link' => $action->isLink(),
        ]);
        $attr['class'] = sprintf('%s %s%s',
                                 'action-button btn',
                                 $action->getButtonStyle(),
                                 isset($attr['class']) ? ' '.trim($attr['class']) : '');
        $attr['href'] = $action->isLink() ?
                      $action->getLink($entity, $this->crudUrlGenerator, $this->urlGenerator) :
                      $this->getActionHref($action);

        return $twig->render('@PerformBase/action/_button.html.twig', [
            'label' => $label,
            'attr' => $attr,
        ]);
    }

    public function actionOption(\Twig_Environment $twig, ConfiguredAction $action, $crudName, $context)
    {
        $label = $action->getBatchLabel($this->request);

        $attr = [];
        $attr['data-action'] = json_encode([
            'crudName' => $crudName,
            'label' => $label,
            'context' => $context,
            //need to change the message depending on the number of entities - ajax?
            'message' => 'Are you sure you want to do this?',
            'confirm' => $action->isConfirmationRequired(),
            'buttonStyle' => $action->getButtonStyle(),
        ]);
        $attr['value'] = $this->getActionHref($action);

        return $twig->render('@PerformBase/action/_option.html.twig', [
            'attr' => $attr,
            'label' => $label,
        ]);
    }

    public function buttonsForEntity($crudName, $entity)
    {
        return $this->store->getActionConfig($crudName)->getButtonsForEntity($entity, $this->request);
    }

    public function getName()
    {
        return 'perform_action';
    }

    private function getActionHref(ConfiguredAction $action)
    {
        try {
            return $this->urlGenerator->generate('perform_base_action_index', ['action' => $action->getName()]);
        } catch (RouteNotFoundException $e) {
            throw MissingResourceException::create($e, '@PerformBaseBundle/Resources/config/routing/actions.yml', 'to use action buttons', 'perform_base_action_index');
        }
    }
}
