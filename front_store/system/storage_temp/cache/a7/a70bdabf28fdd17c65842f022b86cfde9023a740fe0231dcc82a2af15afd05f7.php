<?php

/* extension/dashboard/activity_info.twig */
class __TwigTemplate_c50fef1b84d2e22a7b9eacaacfeb8d7461bd042e7115ef9f4fb1d7e89cba2488 extends Twig_Template
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
        echo "<div class=\"tile tile-primary\">
  <div class=\"tile-heading\">In transit shipment <span class=\"pull-right\">
    ";
        // line 3
        if (((isset($context["percentage"]) ? $context["percentage"] : null) > 0)) {
            // line 4
            echo "    <i class=\"fa fa-caret-up\"></i>
    ";
        } elseif ((        // line 5
(isset($context["percentage"]) ? $context["percentage"] : null) < 0)) {
            // line 6
            echo "    <i class=\"fa fa-caret-down\"></i>
    ";
        }
        // line 8
        echo "    ";
        echo (isset($context["percentage"]) ? $context["percentage"] : null);
        echo "%</span></div>
  <div class=\"tile-body\"><i class=\"fa fa-user\"></i>
    <h2 class=\"pull-right\">";
        // line 10
        echo (isset($context["total"]) ? $context["total"] : null);
        echo "</h2>
  </div>
  <div class=\"tile-footer\"><a href=\"";
        // line 12
        echo (isset($context["customer"]) ? $context["customer"] : null);
        echo "\">";
        echo (isset($context["text_view"]) ? $context["text_view"] : null);
        echo "</a></div>
</div>
 ";
    }

    public function getTemplateName()
    {
        return "extension/dashboard/activity_info.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 12,  40 => 10,  34 => 8,  30 => 6,  28 => 5,  25 => 4,  23 => 3,  19 => 1,);
    }
}
/* <div class="tile tile-primary">*/
/*   <div class="tile-heading">In transit shipment <span class="pull-right">*/
/*     {% if percentage > 0 %}*/
/*     <i class="fa fa-caret-up"></i>*/
/*     {% elseif percentage < 0 %}*/
/*     <i class="fa fa-caret-down"></i>*/
/*     {% endif %}*/
/*     {{ percentage }}%</span></div>*/
/*   <div class="tile-body"><i class="fa fa-user"></i>*/
/*     <h2 class="pull-right">{{ total }}</h2>*/
/*   </div>*/
/*   <div class="tile-footer"><a href="{{ customer }}">{{ text_view }}</a></div>*/
/* </div>*/
/*  */
