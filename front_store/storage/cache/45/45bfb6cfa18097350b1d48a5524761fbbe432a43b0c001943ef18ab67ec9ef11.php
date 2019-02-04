<?php

/* techeco/template/extension/module/category_tab.twig */
class __TwigTemplate_87605c1713b99b1d479673ec940b20d03df802f409d381449ba0a56f50910bce extends Twig_Template
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
        echo "<div class=\"category-tab\">
    <h1 class=\"heading\">";
        // line 2
        echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
        echo "</h1>
     <button type=\"button\" class=\"btn toggle collapsed catb\" data-toggle=\"collapse\" data-target=\"#cat_tab\"></button>
    <!--category-tab-->
    <div class=\"row\">
    <div class=\"col-lg-3 col-md-4 col-sm-6 col-xs-12 home-cat collapse footer-collapse\" id=\"cat_tab\">
        <ul class=\"nav nav-tabs\">

        ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["categories"]) ? $context["categories"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 10
            echo "            ";
            if (($this->getAttribute($context["loop"], "index0", array()) == 0)) {
                // line 11
                echo "              ";
                $context["class"] = " class=\"active webTab\"";
                // line 12
                echo "            ";
            } else {
                // line 13
                echo "              ";
                $context["class"] = " class=\"webTab\"";
                // line 14
                echo "            ";
            }
            // line 15
            echo "            <li";
            echo (isset($context["class"]) ? $context["class"] : null);
            echo "><a href=\"#tab-";
            echo $this->getAttribute($context["loop"], "index", array());
            echo "\" data-toggle=\"tab\">";
            echo $this->getAttribute($context["category"], "category", array());
            echo "</a></li>
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "        </ul></div>


    <div class=\"tab-content col-lg-9 col-md-8 col-sm-6 col-xs-12\">
        
        ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["categories"]) ? $context["categories"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["products"]) {
            // line 23
            echo "        ";
            if (($this->getAttribute($context["loop"], "index0", array()) == 0)) {
                // line 24
                echo "          ";
                $context["class"] = " active in";
                // line 25
                echo "        ";
            } else {
                // line 26
                echo "          ";
                $context["class"] = "";
                // line 27
                echo "        ";
            }
            // line 28
            echo "        <div class=\"tab-pane fade";
            echo (isset($context["class"]) ? $context["class"] : null);
            echo " row\" id=\"tab-";
            echo $this->getAttribute($context["loop"], "index", array());
            echo "\">
            <div id=\"cattab\" class=\"owl-theme owl-carousel\">
          ";
            // line 30
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["products"], "products", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 31
                echo "                <div class=\"product-layout col-xs-12\">
                    <div class=\"product-thumb transition\">
                        <div class=\"image\"><a href=\"";
                // line 33
                echo $this->getAttribute($context["product"], "href", array());
                echo "\"><img src=\"";
                echo $this->getAttribute($context["product"], "thumb", array());
                echo "\" alt=\"";
                echo $this->getAttribute($context["product"], "name", array());
                echo "\" class=\"center-block img-responsive\" /></a></div>
                        <div class=\"caption\">
        <h4><a href=\"";
                // line 35
                echo $this->getAttribute($context["product"], "href", array());
                echo "\">";
                echo $this->getAttribute($context["product"], "name", array());
                echo "</a></h4>

         ";
                // line 37
                if ($this->getAttribute($context["product"], "price", array())) {
                    // line 38
                    echo "        <div class=\"price\">
          ";
                    // line 39
                    if ( !$this->getAttribute($context["product"], "special", array())) {
                        // line 40
                        echo "          ";
                        echo $this->getAttribute($context["product"], "price", array());
                        echo "
          ";
                    } else {
                        // line 42
                        echo "          <span class=\"price-new\">";
                        echo $this->getAttribute($context["product"], "special", array());
                        echo "</span> <span class=\"price-old\">";
                        echo $this->getAttribute($context["product"], "price", array());
                        echo "</span>
          ";
                    }
                    // line 44
                    echo "         ";
                    // line 47
                    echo "        </div>
        ";
                }
                // line 49
                echo "        ";
                // line 50
                echo "        ";
                if ($this->getAttribute($context["product"], "rating", array())) {
                    // line 51
                    echo "          <div class=\"rating\">
            ";
                    // line 52
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(range(1, 5));
                    foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                        // line 53
                        echo "            ";
                        if (($this->getAttribute($context["product"], "rating", array()) < $context["i"])) {
                            // line 54
                            echo "            <span class=\"fa fa-stack\">
              <i class=\"fa fa-star-o fa-stack-2x\"></i>
            </span>
            ";
                        } else {
                            // line 58
                            echo "            <span class=\"fa fa-stack\">
              <i class=\"fa fa-star fa-stack-2x\"></i><i class=\"fa fa-star-o fa-stack-2x\"></i>
            </span>
            ";
                        }
                        // line 62
                        echo "          ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    echo "</div>";
                } else {
                    // line 63
                    echo "          <div class=\"rating\">";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(range(1, 5));
                    foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                        // line 64
                        echo "          <span class=\"fa fa-stack\"><i class=\"fa fa-star-o fa-stack-2x\"></i></span>
          ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 65
                    echo "</div>
        ";
                }
                // line 67
                echo "       
         <div class=\"button-group\">
          <button type=\"button\" data-toggle=\"tooltip\"  onclick=\"cart.add('";
                // line 69
                echo $this->getAttribute($context["product"], "product_id", array());
                echo "');\" class=\"bcart\"><span>";
                echo (isset($context["button_cart"]) ? $context["button_cart"] : null);
                echo "</span></button>
        <button type=\"button\" data-toggle=\"tooltip\" title=\"";
                // line 70
                echo (isset($context["button_wishlist"]) ? $context["button_wishlist"] : null);
                echo "\" onclick=\"wishlist.add('";
                echo $this->getAttribute($context["product"], "product_id", array());
                echo "');\" class=\"bwish pull-right\"><svg width=\"18px\" height=\"17px\"><use xlink:href=\"#heart\" /></svg></button>
        <button type=\"button\" data-toggle=\"tooltip\" title=\"";
                // line 71
                echo (isset($context["button_compare"]) ? $context["button_compare"] : null);
                echo "\" onclick=\"compare.add('";
                echo $this->getAttribute($context["product"], "product_id", array());
                echo "');\" class=\"bcom pull-right\"><svg width=\"18px\" height=\"17px\"><use xlink:href=\"#compare\"/></svg></button>
        <div class=\"bquickv pull-right\" title=\"Quickview\" data-toggle=\"tooltip\"></div>
      </div>
      </div>
                    </div>
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 78
            echo "            </div>
        </div>
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['products'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 81
        echo "    </div>
</div>
</div>
<!--/category-tab-->

<script type=\"text/javascript\">
    \$(document).ready(function() {
    \$(\".tab-content .tab-pane #cattab\").owlCarousel({
    itemsCustom : [
    [0, 1],
    [600, 1],
    [768, 1],
    [992, 2],
    [1200, 2],
    [1590, 3]
    ],
      // autoPlay: 1000,
      navigationText: ['<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>', '<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>'],
      navigation : true,
      pagination:false
    });
    });
</script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/category_tab.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  281 => 81,  265 => 78,  250 => 71,  244 => 70,  238 => 69,  234 => 67,  230 => 65,  223 => 64,  218 => 63,  210 => 62,  204 => 58,  198 => 54,  195 => 53,  191 => 52,  188 => 51,  185 => 50,  183 => 49,  179 => 47,  177 => 44,  169 => 42,  163 => 40,  161 => 39,  158 => 38,  156 => 37,  149 => 35,  140 => 33,  136 => 31,  132 => 30,  124 => 28,  121 => 27,  118 => 26,  115 => 25,  112 => 24,  109 => 23,  92 => 22,  85 => 17,  64 => 15,  61 => 14,  58 => 13,  55 => 12,  52 => 11,  49 => 10,  32 => 9,  22 => 2,  19 => 1,);
    }
}
/* <div class="category-tab">*/
/*     <h1 class="heading">{{heading_title}}</h1>*/
/*      <button type="button" class="btn toggle collapsed catb" data-toggle="collapse" data-target="#cat_tab"></button>*/
/*     <!--category-tab-->*/
/*     <div class="row">*/
/*     <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 home-cat collapse footer-collapse" id="cat_tab">*/
/*         <ul class="nav nav-tabs">*/
/* */
/*         {% for category in categories %}*/
/*             {% if loop.index0 == 0 %}*/
/*               {% set class = ' class="active webTab"' %}*/
/*             {% else %}*/
/*               {% set class = ' class="webTab"' %}*/
/*             {% endif %}*/
/*             <li{{class}}><a href="#tab-{{loop.index}}" data-toggle="tab">{{category.category}}</a></li>*/
/*         {% endfor %}*/
/*         </ul></div>*/
/* */
/* */
/*     <div class="tab-content col-lg-9 col-md-8 col-sm-6 col-xs-12">*/
/*         */
/*         {% for products in categories %}*/
/*         {% if loop.index0 == 0 %}*/
/*           {% set class = ' active in' %}*/
/*         {% else %}*/
/*           {% set class = ''%}*/
/*         {% endif %}*/
/*         <div class="tab-pane fade{{class}} row" id="tab-{{loop.index}}">*/
/*             <div id="cattab" class="owl-theme owl-carousel">*/
/*           {% for product in products.products %}*/
/*                 <div class="product-layout col-xs-12">*/
/*                     <div class="product-thumb transition">*/
/*                         <div class="image"><a href="{{ product.href }}"><img src="{{product.thumb}}" alt="{{product.name}}" class="center-block img-responsive" /></a></div>*/
/*                         <div class="caption">*/
/*         <h4><a href="{{ product.href }}">{{ product.name }}</a></h4>*/
/* */
/*          {% if product.price %}*/
/*         <div class="price">*/
/*           {% if not product.special %}*/
/*           {{ product.price }}*/
/*           {% else %}*/
/*           <span class="price-new">{{ product.special }}</span> <span class="price-old">{{ product.price }}</span>*/
/*           {% endif %}*/
/*          {#  {% if product.tax %}*/
/*           <span class="price-tax">{{ text_tax }} {{ product.tax }}</span>*/
/*           {% endif %} #}*/
/*         </div>*/
/*         {% endif %}*/
/*         {# <p>{{ product.description }}</p> #}*/
/*         {% if product.rating %}*/
/*           <div class="rating">*/
/*             {% for i in 1..5 %}*/
/*             {% if product.rating < i %}*/
/*             <span class="fa fa-stack">*/
/*               <i class="fa fa-star-o fa-stack-2x"></i>*/
/*             </span>*/
/*             {% else %}*/
/*             <span class="fa fa-stack">*/
/*               <i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i>*/
/*             </span>*/
/*             {% endif %}*/
/*           {% endfor %}</div>{% else %}*/
/*           <div class="rating">{% for i in 1..5 %}*/
/*           <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>*/
/*           {% endfor %}</div>*/
/*         {% endif %}*/
/*        */
/*          <div class="button-group">*/
/*           <button type="button" data-toggle="tooltip"  onclick="cart.add('{{ product.product_id }}');" class="bcart"><span>{{ button_cart }}</span></button>*/
/*         <button type="button" data-toggle="tooltip" title="{{ button_wishlist }}" onclick="wishlist.add('{{ product.product_id }}');" class="bwish pull-right"><svg width="18px" height="17px"><use xlink:href="#heart" /></svg></button>*/
/*         <button type="button" data-toggle="tooltip" title="{{ button_compare }}" onclick="compare.add('{{ product.product_id }}');" class="bcom pull-right"><svg width="18px" height="17px"><use xlink:href="#compare"/></svg></button>*/
/*         <div class="bquickv pull-right" title="Quickview" data-toggle="tooltip"></div>*/
/*       </div>*/
/*       </div>*/
/*                     </div>*/
/*                 </div>*/
/*             {% endfor %}*/
/*             </div>*/
/*         </div>*/
/*         {% endfor %}*/
/*     </div>*/
/* </div>*/
/* </div>*/
/* <!--/category-tab-->*/
/* */
/* <script type="text/javascript">*/
/*     $(document).ready(function() {*/
/*     $(".tab-content .tab-pane #cattab").owlCarousel({*/
/*     itemsCustom : [*/
/*     [0, 1],*/
/*     [600, 1],*/
/*     [768, 1],*/
/*     [992, 2],*/
/*     [1200, 2],*/
/*     [1590, 3]*/
/*     ],*/
/*       // autoPlay: 1000,*/
/*       navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],*/
/*       navigation : true,*/
/*       pagination:false*/
/*     });*/
/*     });*/
/* </script>*/
