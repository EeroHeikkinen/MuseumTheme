 {js filename="jquery.jloupe.js"}
 {literal}
  <!-- Thumbnail magnifier -->
  <script type="text/javascript">
    $(document).ready(function() {
        $("img.summcover").jloupe({
            width: 320,
            height: 320,
            radiusLT: 160,
            radiusLB: 160,
            radiusRB: 160,
            radiusRT: 160,
            cursorOffsetX: 10,
            cursorOffsetY: 10,
            margin: 15,
            borderColor: false,
            image: '../interface/themes/museum/images/loupe/loupe_320.png',
            imageInv: '../interface/themes/museum/images/loupe/loupe_320_inv.png'
        });
    });
  </script>
 {/literal}