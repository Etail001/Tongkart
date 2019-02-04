<?php

/* techeco/template/extension/module/imgbanner.twig */
class __TwigTemplate_25f7827ec2618e27b2a553bc545a556e863ed40b984e1568cc1e748fd742cd87 extends Twig_Template
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
        echo "
<div class=\"imgbanner hidden-xs\">
\t\t\t";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["banners"]) ? $context["banners"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["banner"]) {
            // line 4
            echo "\t\t\t\t<div class=\" beffect\">
\t\t\t\t  ";
            // line 5
            if ($this->getAttribute($context["banner"], "link", array())) {
                // line 6
                echo "\t\t\t\t  \t<a href=\"";
                echo $this->getAttribute($context["banner"], "link", array());
                echo "\">
\t\t\t\t  \t\t<img src=\"";
                // line 7
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" />
\t\t\t\t  \t</a>
\t\t\t\t  ";
            } else {
                // line 10
                echo "\t\t\t\t  \t<img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" />";
            }
            // line 11
            echo "\t\t\t\t</div>
\t\t\t  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/imgbanner.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 13,  52 => 11,  45 => 10,  37 => 7,  32 => 6,  30 => 5,  27 => 4,  23 => 3,  19 => 1,);
    }
}
/* */
/* <div class="imgbanner hidden-xs">*/
/* 			{% for banner in banners %}*/
/* 				<div class=" beffect">*/
/* 				  {% if banner.link %}*/
/* 				  	<a href="{{ banner.link }}">*/
/* 				  		<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" />*/
/* 				  	</a>*/
/* 				  {% else %}*/
/* 				  	<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" />{% endif %}*/
/* 				</div>*/
/* 			  {% endfor %}*/
/* </div>*/
/* */
