<?php

/* techeco/template/common/cart.twig */
class __TwigTemplate_832296411edc5837f2386cc4e13c46635200ac777b2c62ca944ac56592f2d505 extends Twig_Template
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
        echo "<div id=\"cart\" class=\"btn-group btn-block text-left\">
  <button type=\"button\" data-toggle=\"dropdown\" data-loading-text=\"";
        // line 2
        echo (isset($context["text_loading"]) ? $context["text_loading"] : null);
        echo "\" class=\"dropdown-toggle\">
 <li><svg width=\"32px\" height=\"31px\"> <use xlink:href=\"#hcart\"></use></svg></li>
 <li class=\"text-left\"><h1>my cart</h1><h2 id=\"cart-total\">";
        // line 4
        echo (isset($context["text_items"]) ? $context["text_items"] : null);
        echo "</h2></li>
 </button>
  <ul class=\"dropdown-menu pull-right\">
    ";
        // line 7
        if (((isset($context["products"]) ? $context["products"] : null) || (isset($context["vouchers"]) ? $context["vouchers"] : null))) {
            // line 8
            echo "    <li>
      <div>
        ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["products"]) ? $context["products"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 11
                echo "        <div class=\" col-xs-12 cartdrop\">
          <div class=\"pull-left\">";
                // line 12
                if ($this->getAttribute($context["product"], "thumb", array())) {
                    echo " <a href=\"";
                    echo $this->getAttribute($context["product"], "href", array());
                    echo "\"><img src=\"";
                    echo $this->getAttribute($context["product"], "thumb", array());
                    echo "\" alt=\"";
                    echo $this->getAttribute($context["product"], "name", array());
                    echo "\" title=\"";
                    echo $this->getAttribute($context["product"], "name", array());
                    echo "\" class=\"img-thumbnail\" /></a> ";
                }
                echo "</div>
          <div class=\"pull-left cartname\"><a href=\"";
                // line 13
                echo $this->getAttribute($context["product"], "href", array());
                echo "\">";
                echo $this->getAttribute($context["product"], "name", array());
                echo "</a> ";
                if ($this->getAttribute($context["product"], "option", array())) {
                    // line 14
                    echo "           
            ";
                }
                // line 16
                echo "            ";
                if ($this->getAttribute($context["product"], "recurring", array())) {
                    echo " <br />
            - <small>";
                    // line 17
                    echo (isset($context["text_recurring"]) ? $context["text_recurring"] : null);
                    echo " ";
                    echo $this->getAttribute($context["product"], "recurring", array());
                    echo "</small> ";
                }
                // line 18
                echo "          </div>
           <div class=\"pull-right\"><button type=\"button\" onclick=\"cart.remove('";
                // line 19
                echo $this->getAttribute($context["product"], "cart_id", array());
                echo "');\" title=\"";
                echo (isset($context["button_remove"]) ? $context["button_remove"] : null);
                echo "\" class=\"btn btn-danger btn-xs\"><i class=\"fa fa-times\"></i></button></div>

          <div class=\"cartprice\">
            <span>";
                // line 22
                echo $this->getAttribute($context["product"], "quantity", array());
                echo " x</span>
            <span>";
                // line 23
                echo $this->getAttribute($context["product"], "total", array());
                echo "</span>
          </div>
         
        </div>


        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["vouchers"]) ? $context["vouchers"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["voucher"]) {
                // line 31
                echo "        <tr>
          <td class=\"text-center\"></td>
          <td class=\"text-left\">";
                // line 33
                echo $this->getAttribute($context["voucher"], "description", array());
                echo "</td>
          <td class=\"text-right\">x&nbsp;1</td>
          <td class=\"text-right\">";
                // line 35
                echo $this->getAttribute($context["voucher"], "amount", array());
                echo "</td>
          <td class=\"text-center text-danger\"><button type=\"button\" onclick=\"voucher.remove('";
                // line 36
                echo $this->getAttribute($context["voucher"], "key", array());
                echo "');\" title=\"";
                echo (isset($context["button_remove"]) ? $context["button_remove"] : null);
                echo "\" class=\"btn btn-danger btn-xs\"><i class=\"fa fa-times\"></i></button></td>
        </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['voucher'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 39
            echo "      </div>
    </li>
    <li>
      <div>
        <table class=\"table table-bordered\">
          ";
            // line 44
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["totals"]) ? $context["totals"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["total"]) {
                // line 45
                echo "          <tr>
            <td class=\"text-right\"><strong>";
                // line 46
                echo $this->getAttribute($context["total"], "title", array());
                echo "</strong></td>
            <td class=\"text-right\">";
                // line 47
                echo $this->getAttribute($context["total"], "text", array());
                echo "</td>
          </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['total'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 50
            echo "        </table>
        <p class=\"text-center cartbt\"><a href=\"";
            // line 51
            echo (isset($context["cart"]) ? $context["cart"] : null);
            echo "\" class=\"btn btn-primary\"><i class=\"fa fa-shopping-cart\"></i> ";
            echo (isset($context["text_cart"]) ? $context["text_cart"] : null);
            echo "</a>&nbsp;&nbsp;&nbsp;<a href=\"";
            echo (isset($context["checkout"]) ? $context["checkout"] : null);
            echo "\" class=\"btn btn-primary\"><i class=\"fa fa-share\"></i> ";
            echo (isset($context["text_checkout"]) ? $context["text_checkout"] : null);
            echo "</a></p>
      </div>
    </li>
    ";
        } else {
            // line 55
            echo "    <li>
      <p class=\"text-center\">";
            // line 56
            echo (isset($context["text_empty"]) ? $context["text_empty"] : null);
            echo "</p>
    </li>
    ";
        }
        // line 59
        echo "  </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "techeco/template/common/cart.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  190 => 59,  184 => 56,  181 => 55,  168 => 51,  165 => 50,  156 => 47,  152 => 46,  149 => 45,  145 => 44,  138 => 39,  127 => 36,  123 => 35,  118 => 33,  114 => 31,  109 => 30,  96 => 23,  92 => 22,  84 => 19,  81 => 18,  75 => 17,  70 => 16,  66 => 14,  60 => 13,  46 => 12,  43 => 11,  39 => 10,  35 => 8,  33 => 7,  27 => 4,  22 => 2,  19 => 1,);
    }
}
/* <div id="cart" class="btn-group btn-block text-left">*/
/*   <button type="button" data-toggle="dropdown" data-loading-text="{{ text_loading }}" class="dropdown-toggle">*/
/*  <li><svg width="32px" height="31px"> <use xlink:href="#hcart"></use></svg></li>*/
/*  <li class="text-left"><h1>my cart</h1><h2 id="cart-total">{{ text_items }}</h2></li>*/
/*  </button>*/
/*   <ul class="dropdown-menu pull-right">*/
/*     {% if products or vouchers %}*/
/*     <li>*/
/*       <div>*/
/*         {% for product in products %}*/
/*         <div class=" col-xs-12 cartdrop">*/
/*           <div class="pull-left">{% if product.thumb %} <a href="{{ product.href }}"><img src="{{ product.thumb }}" alt="{{ product.name }}" title="{{ product.name }}" class="img-thumbnail" /></a> {% endif %}</div>*/
/*           <div class="pull-left cartname"><a href="{{ product.href }}">{{ product.name }}</a> {% if product.option %}*/
/*            */
/*             {% endif %}*/
/*             {% if product.recurring %} <br />*/
/*             - <small>{{ text_recurring }} {{ product.recurring }}</small> {% endif %}*/
/*           </div>*/
/*            <div class="pull-right"><button type="button" onclick="cart.remove('{{ product.cart_id }}');" title="{{ button_remove }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></div>*/
/* */
/*           <div class="cartprice">*/
/*             <span>{{ product.quantity }} x</span>*/
/*             <span>{{ product.total }}</span>*/
/*           </div>*/
/*          */
/*         </div>*/
/* */
/* */
/*         {% endfor %}*/
/*         {% for voucher in vouchers %}*/
/*         <tr>*/
/*           <td class="text-center"></td>*/
/*           <td class="text-left">{{ voucher.description }}</td>*/
/*           <td class="text-right">x&nbsp;1</td>*/
/*           <td class="text-right">{{ voucher.amount }}</td>*/
/*           <td class="text-center text-danger"><button type="button" onclick="voucher.remove('{{ voucher.key }}');" title="{{ button_remove }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></td>*/
/*         </tr>*/
/*         {% endfor %}*/
/*       </div>*/
/*     </li>*/
/*     <li>*/
/*       <div>*/
/*         <table class="table table-bordered">*/
/*           {% for total in totals %}*/
/*           <tr>*/
/*             <td class="text-right"><strong>{{ total.title }}</strong></td>*/
/*             <td class="text-right">{{ total.text }}</td>*/
/*           </tr>*/
/*           {% endfor %}*/
/*         </table>*/
/*         <p class="text-center cartbt"><a href="{{ cart }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> {{ text_cart }}</a>&nbsp;&nbsp;&nbsp;<a href="{{ checkout }}" class="btn btn-primary"><i class="fa fa-share"></i> {{ text_checkout }}</a></p>*/
/*       </div>*/
/*     </li>*/
/*     {% else %}*/
/*     <li>*/
/*       <p class="text-center">{{ text_empty }}</p>*/
/*     </li>*/
/*     {% endif %}*/
/*   </ul>*/
/* </div>*/
/* */
