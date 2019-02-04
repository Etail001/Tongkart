<?php

/* techeco/template/extension/module/html.twig */
class __TwigTemplate_ea2cf920ff9f9d48c360806d8df3bb95ad3b3ff9dced9775d3b7719b657744a9 extends Twig_Template
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
        echo "<div>";
        if ((isset($context["heading_title"]) ? $context["heading_title"] : null)) {
            // line 2
            echo "  <h2>";
            echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
            echo "</h2>
  ";
        }
        // line 4
        echo "  ";
        echo (isset($context["html"]) ? $context["html"] : null);
        echo "</div>
<script type=\"text/javascript\">
    \$(document).ready(function() {
    \$(\"#testi\").owlCarousel({
    itemsCustom : [
    [0, 1],
    [600, 1],
    [768, 1]
    ],
      // autoPlay: 1000,
      navigationText: ['<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>', '<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>'],
      navigation : false,
      pagination:true
    });
    });
</script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 4,  22 => 2,  19 => 1,);
    }
}
/* <div>{% if heading_title %}*/
/*   <h2>{{ heading_title }}</h2>*/
/*   {% endif %}*/
/*   {{ html }}</div>*/
/* <script type="text/javascript">*/
/*     $(document).ready(function() {*/
/*     $("#testi").owlCarousel({*/
/*     itemsCustom : [*/
/*     [0, 1],*/
/*     [600, 1],*/
/*     [768, 1]*/
/*     ],*/
/*       // autoPlay: 1000,*/
/*       navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],*/
/*       navigation : false,*/
/*       pagination:true*/
/*     });*/
/*     });*/
/* </script>*/
