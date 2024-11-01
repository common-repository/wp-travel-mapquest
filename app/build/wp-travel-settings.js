(()=>{"use strict";const e=window.wp.element,t=window.wp.hooks,l=window.wp.components,a=window.wp.i18n,n=window.wp.data;(0,t.addFilter)("wp_travel_settings_after_maps_fields","wp-travel",(t=>{const{updateSettings:l}=(0,n.dispatch)("WPTravel/Admin"),a=(0,n.useSelect)((e=>e("WPTravel/Admin").getAllStore()),[]),{wp_travel_map:p}=a;return"wp-travel-mapquest"!=p?t:[...t,(0,e.createElement)(r,null)]}),20);const r=()=>{const{updateSettings:t}=(0,n.dispatch)("WPTravel/Admin"),r=(0,n.useSelect)((e=>e("WPTravel/Admin").getAllStore()),[]),{map_quest_api_key:p,map_quest_zoom_level:m}=r;return(0,e.createElement)(e.Fragment,null,(0,e.createElement)(l.PanelRow,null,(0,e.createElement)("label",null,(0,a.__)("MapQuest API Key","wp-travel-mapquest")),(0,e.createElement)("div",{className:"wp-travel-field-value"},(0,e.createElement)(l.TextControl,{value:void 0!==p?p:"",onChange:e=>{t({...r,map_quest_api_key:e})}}),(0,e.createElement)("p",{className:"description",dangerouslySetInnerHTML:{__html:'Don\'t have api key <a href="https://developer.mapquest.com/" target="_blank">click here</a>'}}))),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)("label",null,(0,a.__)("Map Zoom Level","wp-travel-mapquest")),(0,e.createElement)("div",{className:"wp-travel-field-value"},(0,e.createElement)(l.TextControl,{value:void 0!==m?m:15,onChange:e=>{t({...r,map_quest_zoom_level:e})}}))))}})();