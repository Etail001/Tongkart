<?php

/* techeco/template/extension/module/carousel.twig */
class __TwigTemplate_e53466cf8a7caf1629638e8b897c8ea4b734e5b8efba4a1468af035fc11224a9 extends Twig_Template
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
        echo "  <div class=\"logo-slider\">
    <h1 class=\"lefthead\">brands</h1>
    ";
        // line 3
        $context["temp"] = 0;
        // line 4
        $context["setCol"] = 3;
        // line 5
        echo "  <div id=\"carousel";
        echo (isset($context["module"]) ? $context["module"] : null);
        echo "\" class=\"owl-carousel owl-theme\">
    ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["banners"]) ? $context["banners"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["banner"]) {
            // line 7
            echo "    ";
            $context["temp"] = ((isset($context["temp"]) ? $context["temp"] : null) + 1);
            // line 8
            echo "
        ";
            // line 9
            if ((((isset($context["temp"]) ? $context["temp"] : null) % (isset($context["setCol"]) ? $context["setCol"] : null)) == 1)) {
                // line 10
                echo "            <div class=\"multi-row\">
        ";
            }
            // line 12
            echo "      <div class=\"text-center\">
        ";
            // line 13
            if ($this->getAttribute($context["banner"], "link", array())) {
                // line 14
                echo "        <a href=\"";
                echo $this->getAttribute($context["banner"], "link", array());
                echo "\">
          <img src=\"";
                // line 15
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive center-block\" />
        </a>
        ";
            } else {
                // line 18
                echo "        <img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive center-block\" />";
            }
            // line 19
            echo "      </div>
         ";
            // line 20
            if ((((isset($context["temp"]) ? $context["temp"] : null) % (isset($context["setCol"]) ? $context["setCol"] : null)) == 0)) {
                // line 21
                echo "            </div>
        ";
            }
            // line 23
            echo "      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        echo "        ";
        if (((twig_length_filter($this->env, (isset($context["products"]) ? $context["products"] : null)) % (isset($context["setCol"]) ? $context["setCol"] : null)) != 0)) {
            // line 25
            echo "    <!-- <h1>hii</h1> -->
        </div>
    ";
        }
        // line 28
        echo "  </div>

<script type=\"text/javascript\">
    \$(document).ready(function() {
    \$(\"#carousel";
        // line 32
        echo (isset($context["module"]) ? $context["module"] : null);
        echo "\").owlCarousel({
    itemsCustom : [
    [0, 2],
    [600, 2],
    [768, 2],
    [992, 2],
    [1200, 2],
    [1410, 2]
    ],
      autoPlay: false,
      loop: false,
      navigationText: ['<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>', '<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>'],
      navigation : true,
      pagination:false
    });
    });
</script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/carousel.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 32,  96 => 28,  91 => 25,  88 => 24,  82 => 23,  78 => 21,  76 => 20,  73 => 19,  66 => 18,  58 => 15,  53 => 14,  51 => 13,  48 => 12,  44 => 10,  42 => 9,  39 => 8,  36 => 7,  32 => 6,  27 => 5,  25 => 4,  23 => 3,  19 => 1,);
    }
}
/*   <div class="logo-slider">*/
/*     <h1 class="lefthead">brands</h1>*/
/*     {% set temp = 0 %}*/
/* {% set setCol = 3 %}*/
/*   <div id="carousel{{ module }}" class="owl-carousel owl-theme">*/
/*     {% for banner in banners %}*/
/*     {% set temp = temp + 1 %}*/
/* */
/*         {% if temp % setCol == 1 %}*/
/*             <div class="multi-row">*/
/*         {% endif %}*/
/*       <div class="text-center">*/
/*         {% if banner.link %}*/
/*         <a href="{{ banner.link }}">*/
/*           <img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive center-block" />*/
/*         </a>*/
/*         {% else %}*/
/*         <img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive center-block" />{% endif %}*/
/*       </div>*/
/*          {% if temp % setCol == 0 %}*/
/*             </div>*/
/*         {% endif %}*/
/*       {% endfor %}*/
/*         {% if products|length % setCol != 0 %}*/
/*     <!-- <h1>hii</h1> -->*/
/*         </div>*/
/*     {% endif %}*/
/*   </div>*/
/* */
/* <script type="text/javascript">*/
/*     $(document).ready(function() {*/
/*     $("#carousel{{ module }}").owlCarousel({*/
/*     itemsCustom : [*/
/*     [0, 2],*/
/*     [600, 2],*/
/*     [768, 2],*/
/*     [992, 2],*/
/*     [1200, 2],*/
/*     [1410, 2]*/
/*     ],*/
/*       autoPlay: false,*/
/*       loop: false,*/
/*       navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],*/
/*       navigation : true,*/
/*       pagination:false*/
/*     });*/
/*     });*/
/* </script>*/
