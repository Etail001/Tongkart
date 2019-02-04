<?php

/* techeco/template/extension/module/slideshow.twig */
class __TwigTemplate_20ef5416f06a1a77c2c01090bb0f426cddb4199976c0184c9266048c4ed19da8 extends Twig_Template
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
        echo "  <div class=\"row slideborder\">
    <div class=\"col-lg-8 col-md-8 slb\">
  <div id=\"slideshow";
        // line 3
        echo (isset($context["module"]) ? $context["module"] : null);
        echo "\" class=\"owl-carousel owl-theme\">
   ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["banners"]) ? $context["banners"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["banner"]) {
            // line 5
            echo "      <div class=\"text-center\">";
            if ($this->getAttribute($context["banner"], "link", array())) {
                echo "<a href=\"";
                echo $this->getAttribute($context["banner"], "link", array());
                echo "\"><img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" /></a>";
            } else {
                echo "<img src=\"";
                echo $this->getAttribute($context["banner"], "image", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["banner"], "title", array());
                echo "\" class=\"img-responsive\" />";
            }
            echo "</div>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 7
        echo "    </div>
</div>

<script type=\"text/javascript\">
    \$(document).ready(function() {
    \$(\"#slideshow";
        // line 12
        echo (isset($context["module"]) ? $context["module"] : null);
        echo "\").owlCarousel({
    itemsCustom : [
    [0, 1]
    ],
      autoPlay: 10000,
      animateIn: 'fadeIn',
      animateOut: 'fadeOut',
      loop: false,
      navigationText: ['<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>', '<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>'],
      navigation : false,
      pagination:true
    });
    });
</script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/slideshow.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 12,  54 => 7,  31 => 5,  27 => 4,  23 => 3,  19 => 1,);
    }
}
/*   <div class="row slideborder">*/
/*     <div class="col-lg-8 col-md-8 slb">*/
/*   <div id="slideshow{{ module }}" class="owl-carousel owl-theme">*/
/*    {% for banner in banners %}*/
/*       <div class="text-center">{% if banner.link %}<a href="{{ banner.link }}"><img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" /></a>{% else %}<img src="{{ banner.image }}" alt="{{ banner.title }}" class="img-responsive" />{% endif %}</div>*/
/*       {% endfor %}*/
/*     </div>*/
/* </div>*/
/* */
/* <script type="text/javascript">*/
/*     $(document).ready(function() {*/
/*     $("#slideshow{{ module }}").owlCarousel({*/
/*     itemsCustom : [*/
/*     [0, 1]*/
/*     ],*/
/*       autoPlay: 10000,*/
/*       animateIn: 'fadeIn',*/
/*       animateOut: 'fadeOut',*/
/*       loop: false,*/
/*       navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],*/
/*       navigation : false,*/
/*       pagination:true*/
/*     });*/
/*     });*/
/* </script>*/
