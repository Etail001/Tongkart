<?php

/* techeco/template/extension/module/sellbanner.twig */
class __TwigTemplate_2f6ccbaf6c46e9e96897916acbca12b078c9c3ca1200fccf3953d468e54665e0 extends Twig_Template
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
        echo "<div class=\"col-lg-4 col-md-4 hidden-xs hidden-sm sellbanner\">
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
                echo "\" class=\"img-responsive center-block\" />
\t\t\t\t  \t</a>
\t\t\t\t  ";
            } else {
                // line 9
                echo "\t\t\t\t  \t<img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive center-block\" />";
            }
            // line 10
            echo "\t\t\t\t</div>
\t\t\t  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        echo "\t</div>
</div>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/sellbanner.twig";
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
/* <div class="col-lg-4 col-md-4 hidden-xs hidden-sm sellbanner">*/
/* 			{% for banner in banners %}*/
/* 				<div class="beffect">*/
/* 				  {% if banner.link %}*/
/* 				  	<a href="{{ banner.link }}">*/
/* 				  		<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive center-block" />*/
/* 				  	</a>*/
/* 				  {% else %}*/
/* 				  	<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive center-block" />{% endif %}*/
/* 				</div>*/
/* 			  {% endfor %}*/
/* 	</div>*/
/* </div>*/
