<?php

namespace service;

use interfaces\containerAwaireInterface;
use interfaces\UserInterface;
use Pagerfanta\View\TwitterBootstrap3View;
use Render\BasicRenderComponent;
use Render\RenderComponent;
use Symfony\Component\Form\FormView;

class ApplicationTwigPlus extends \Twig_Extension implements containerAwaireInterface
{
    private $container;

    /**
     * @var \Symfony\Component\Form\FormRendererInterface
     */
    private $renderer;

    public function __construct($container)
    {
        $this->setContainer($container);
        $this->renderer = ($container->has("twig.form.renderer")) ? $container->get("twig.form.renderer") : null;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter("renderComponent", array($this, "renderComponent"), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter("limitstring", array($this, "limitstring")),
            new \Twig_SimpleFilter("relativeDate", array($this, "relativeDate")),
            new \Twig_SimpleFilter("relativetimelive", array($this, "relativeDate")),
            new \Twig_SimpleFilter("pagination", array($this, "pagination"), array('is_safe' => array('html')))
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction("path", array($this, "path")),
            new \Twig_SimpleFunction("url", array($this, "url")),
            new \Twig_SimpleFunction("asset", array($this, "asset")),
            new \Twig_SimpleFunction("form_javascript", array($this, 'renderJavascript'), array("is_safe" => array("html"))),
            new \Twig_SimpleFunction("genReqId", array($this, "genReqId")),
            new \Twig_SimpleFunction("is_authentificated", array($this, "is_authentificated")),
            new \Twig_SimpleFunction("is_granted", array($this, "is_granted")),
            new \Twig_SimpleFunction("round", "round"),
            new \Twig_SimpleFunction("var_export", "var_export"),
            new \Twig_SimpleFunction("assets_url", array($this, "assets_url")),
        );
    }

    public function assets_url($file)
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getContainer()->get("staticdomaine")."/assets/".$file;
    }

    public function genReqId()
    {
        static $reqId = null;

        if($reqId === null) $reqId = uniqid("request_");

        return $reqId;
    }

    public function relativeDate($date)
    {
        if($date instanceof \DateTime) $date = $date->getTimestamp();

        if($date == 0) return "";

        // Déduction de la date donnée à la date actuelle
        $time = time() - intval($date);

        // Calcule si le temps est passé ou à venir
        if ($time > 0) {
            $when = "il y a";
        } else if ($time < 0) {
            $when = "dans environ";
        } else {
            return "il y a moins d'une seconde";
        }
        $time = abs($time);

        // Tableau des unités et de leurs valeurs en secondes
        $times = array( 31104000 =>  'an{s}',       // 12 * 30 * 24 * 60 * 60 secondes
                        2592000  =>  'mois',        // 30 * 24 * 60 * 60 secondes
                        86400    =>  'jour{s}',     // 24 * 60 * 60 secondes
                        3600     =>  'heure{s}',    // 60 * 60 secondes
                        60       =>  'minute{s}',   // 60 secondes
                        1        =>  'seconde{s}'); // 1 seconde

        foreach ($times as $seconds => $unit) {
            // Calcule le delta entre le temps et l'unité donnée
            $delta = round($time / $seconds);

            // Si le delta est supérieur à 1
            if ($delta >= 1) {
                // L'unité est au singulier ou au pluriel ?
                if ($delta == 1) {
                    $unit = str_replace('{s}', '', $unit);
                } else {
                    $unit = str_replace('{s}', 's', $unit);
                }
                // Retourne la chaine adéquate
                return $when." ".$delta." ".$unit;
            }
        }
    }

    public function pagination($pagerFanta)
    {
        return (new TwitterBootstrap3View())->render($pagerFanta, function($page)
        {
            return "?p=".$page;
        });
    }


    public function limitstring($value)
    {
        return (strlen($value) > 35) ? substr($value, 0, 35)."..." : $value;
    }

    public function is_authentificated()
    {
        $profile = ($this->getContainer()->has("profile")) ? $this->getContainer()->get("profile") : null;

        return $profile !== null;
    }

    public function is_granted($role)
    {
        $profile = ($this->getContainer()->has("profile")) ? $this->getContainer()->get("profile") : null;

        return ($profile !== null) ? in_array($profile, $profile->getRoles()) : false;
    }

    public function renderJavascript(FormView $view)
    {
        return $this->renderer->searchAndRenderBlock($view, "javascript");
    }

    public function asset($name)
    {
        return $this->getContainer()->get("router")->generate($name, array(), true, true, $this->getContainer()->get("staticdomaine"));
    }

    public function path($name, $param = array())
    {
        return $this->getContainer()->get("router")->generate($name, $param);
    }

    public function url($name, $params = array())
    {
        return $this->getContainer()->get("router")->generate($name, $params, true);
    }


    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function renderComponent(RenderComponent $renderComponent = null, UserInterface $user = null, $mode = RenderComponent::VIEW_HTML)
    {
        if($renderComponent === null) return;

        if($renderComponent instanceof containerAwaireInterface) $renderComponent->setContainer($this->getContainer());

        return $renderComponent->getRender(RenderComponent::VIEW_HTML, $user, $mode);
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "application.twig.plus";
    }
}