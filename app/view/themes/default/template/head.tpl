  <head>
    <title>{{title}} | {{site_name}}</title>
    <meta charset="utf-8" />
    <meta name="description" content="{{description}}">@@meta@@
<!-- Layout and stylesheets -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
@@common@@
      /* Desktop */
      @media screen and (min-width: 1281px) {
@@desktop-common@@
@@desktop-typified@@
      }

      /* Laptop */
      @media screen and (min-width: 1025px) and (max-width: 1280px) {
@@laptop-common@@
@@laptop-typified@@
      }

      /* Tablet */
      @media screen and (min-width: 769px) and (max-width: 1024px) {
@@tablet-common@@
@@tablet-typified@@
      }

      /* Mobile */
      @media screen and (max-width: 768px) {
@@mobile-common@@
@@mobile-typified@@
      }

    </style>
  </head>