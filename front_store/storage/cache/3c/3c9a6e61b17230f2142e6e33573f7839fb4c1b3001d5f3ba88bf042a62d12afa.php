<?php

/* techeco/template/extension/module/centerbanner.twig */
class __TwigTemplate_30ddb0c1f5edb3465f23192801c5707653d421347a7cb0690c6f829ae8624be6 extends Twig_Template
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
        echo "<div class=\"cbanner hidden-xs\">
\t\t\t";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["banners"]) ? $context["banners"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["banner"]) {
            // line 3
            echo "\t\t\t\t<div class=\"beffect\">
\t\t\t\t  ";
            // line 4
            if ($this->getAttribute($context["banner"], "link", array())) {
                // line 5
                echo "\t\t\t\t  \t<a href=\"";
                echo $this->getAttribute($context["banner"], "link", array());
                echo "\">
\t\t\t\t  \t\t<img src=\"";
                // line 6
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" />
\t\t\t\t  \t</a>
\t\t\t\t  ";
            } else {
                // line 9
                echo "\t\t\t\t  \t<img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" />";
            }
            // line 10
            echo "\t\t\t\t</div>
\t\t\t  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/centerbanner.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 12,  51 => 10,  44 => 9,  36 => 6,  31 => 5,  29 => 4,  26 => 3,  22 => 2,  19 => 1,);
    }
}
/* <div class="cbanner hidden-xs">*/
/* 			{% for banner in banners %}*/
/* 				<div class="beffect">*/
/* 				  {% if banner.link %}*/
/* 				  	<a href="{{ banner.link }}">*/
/* 				  		<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" />*/
/* 				  	</a>*/
/* 				  {% else %}*/
/* 				  	<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" />{% endif %}*/
/* 				</div>*/
/* 			  {% endfor %}*/
/* </div>*/
