<?php

/* Teste/index.html.twig */
class __TwigTemplate_479537e979511a6d5c412b9075efa8da5cf6e29f0993f1802df6361036ce920c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "Hello ";
        echo twig_escape_filter($this->env, (isset($context["nome"]) ? $context["nome"] : null), "html", null, true);
        echo "!";
    }

    public function getTemplateName()
    {
        return "Teste/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
/* Hello {{ nome }}!*/
