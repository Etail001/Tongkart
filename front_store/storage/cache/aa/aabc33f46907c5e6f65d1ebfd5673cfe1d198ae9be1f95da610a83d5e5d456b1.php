<?php

/* techeco/template/common/home.twig */
class __TwigTemplate_c57ba67227e445720833839b4639dffb3982a1ab7d70ecab0a5d4f5350dbef30 extends Twig_Template
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
        echo (isset($context["header"]) ? $context["header"] : null);
        echo "
   <div id=\"page-preloader\" class=\"visible\">
      <div class=\"preloader\">
          <div id=\"loading-center-absolute\">
                <div class=\"object\" id=\"object_one\"></div>
          </div>
      </div>
  </div> 
<div id=\"common-home\" class=\"container\">
  <div class=\"row\">";
        // line 10
        echo (isset($context["column_left"]) ? $context["column_left"] : null);
        echo "
    ";
        // line 11
        if (((isset($context["column_left"]) ? $context["column_left"] : null) && (isset($context["column_right"]) ? $context["column_right"] : null))) {
            // line 12
            echo "    ";
            $context["class"] = "col-sm-6";
            // line 13
            echo "    ";
        } elseif (((isset($context["column_left"]) ? $context["column_left"] : null) || (isset($context["column_right"]) ? $context["column_right"] : null))) {
            // line 14
            echo "    ";
            $context["class"] = "col-sm-8 col-lg-9 col-md-9 col-xs-12 crwidth";
            // line 15
            echo "    ";
        } else {
            // line 16
            echo "    ";
            $context["class"] = "col-sm-12";
            // line 17
            echo "    ";
        }
        // line 18
        echo "    <div id=\"content\" class=\"";
        echo (isset($context["class"]) ? $context["class"] : null);
        echo " home-top\">";
        echo (isset($context["content_top"]) ? $context["content_top"] : null);
        echo "</div>
    ";
        // line 19
        echo (isset($context["column_right"]) ? $context["column_right"] : null);
        echo "</div>
    ";
        // line 20
        echo (isset($context["content_bottom"]) ? $context["content_bottom"] : null);
        echo "
</div>
";
        // line 22
        echo (isset($context["footer"]) ? $context["footer"] : null);
    }

    public function getTemplateName()
    {
        return "techeco/template/common/home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 22,  66 => 20,  62 => 19,  55 => 18,  52 => 17,  49 => 16,  46 => 15,  43 => 14,  40 => 13,  37 => 12,  35 => 11,  31 => 10,  19 => 1,);
    }
}
/* {{ header }}*/
/*    <div id="page-preloader" class="visible">*/
/*       <div class="preloader">*/
/*           <div id="loading-center-absolute">*/
/*                 <div class="object" id="object_one"></div>*/
/*           </div>*/
/*       </div>*/
/*   </div> */
/* <div id="common-home" class="container">*/
/*   <div class="row">{{ column_left }}*/
/*     {% if column_left and column_right %}*/
/*     {% set class = 'col-sm-6' %}*/
/*     {% elseif column_left or column_right %}*/
/*     {% set class = 'col-sm-8 col-lg-9 col-md-9 col-xs-12 crwidth' %}*/
/*     {% else %}*/
/*     {% set class = 'col-sm-12' %}*/
/*     {% endif %}*/
/*     <div id="content" class="{{ class }} home-top">{{ content_top }}</div>*/
/*     {{ column_right }}</div>*/
/*     {{ content_bottom }}*/
/* </div>*/
/* {{ footer }}*/
