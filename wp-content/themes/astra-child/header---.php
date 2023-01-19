
<nav id="main-menu">
        <ul class="menu vertical medium-horizontal {{ main_nav.url }}" data-auto-height="true" data-responsive-menu="drilldown medium-dropdown" data-back-button='<li class="js-drilldown-back"><a tabindex="0">Your Back text</a></li>'>
 
    {% set count = 0 %} // Set main counter
    {% set submenu = 0 %} // Set Submenu Counter
 
    {% for key in main_nav|keys %} // Loop trough the menu elements exposed in the endpoint. 
 
       {% set link =  main_nav[key].url  %} // Retrive first level url
       {% set title =  main_nav[key].title   %} // Retrive first level title
 
       {% if main_nav[key].menu_item_parent == 0 %} // If the parameters "Menu Item Parent" is set to "0" it means that isn't a submenu. 
          {% set parent_id =  main_nav[key].id %} // Set "parent_id" equal to actual id
          <li class="item item_top" data-menu-item-parent="{{ main_nav[key].menu_item_parent }}" data-menu-submenu="{{ submenu }}" data-parentid="{{parent_id}}"> // Print the first level link 
            <a href="{{ main_nav[key].url  }}" class="title">
              {{ main_nav[key].title  }}
            </a>
       {% endif %}
 
       {% if parent_id == main_nav[key].menu_item_parent %} // If "parent_id" and "Menu item parent" are same, it means that we are on a sub menu of the main menu with id "parent_id"
        
          {% if submenu == 0 %}
             
 
            <ul class="sub-menu {{ submenu }}"> // Open the ul for the submenu
 
            {% set subcounter = 0 %} // set a counter of sub menu to 0
            {% for key in main_nav|keys %} // made a loop for know how many sub menu had the "parent_id" menu.
              {% if main_nav[key].menu_item_parent != 0 %} // Don't have submenu
                {% if main_nav[key].menu_item_parent == parent_id %}
                    {% set subcounter = subcounter +1 %} // increment subcounter of one
                  {% endif %}
              {% endif %}
            {% endfor %}
 
            {% if subcounter == 1 %}
               
            {% endif %}
 
 
          {% endif %}
          {% set submenu = submenu +1 %}// increment submenu of one
          <li class="item item_sub" data-subcontainer-lenght="{{subcounter}}" data-menu-item-parent="{{ main_nav[key].menu_item_parent }}" data-menu-submenu="{{ submenu }}" data-menu-subcounter="{{ subcounter }}" data-parentid="{{parent_id}}">
          <a href="<?php echo $link; ?>" class="title"> {{ main_nav[key].title  }} </a>
        </li> // Print submenu items
        {% if main_nav[key].menu_item_parent == parent_id and submenu > subcounter-1 %} // If we are at the last submenu  ( subcounter is equal to the length of the submenu )
         
        {% set submenu = 0 %} 
          </ul> // Close submenu ul
          
 
       {% endif %}
       {% if menu_item_parent != parent_id %}
       </li> // Close main menu that contain submenu
       {% endif %}
       {% endif %}
    {% endfor %}
 
</ul>
 
</nav>