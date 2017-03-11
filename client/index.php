<html>
    <thead>

    <link rel='stylesheet' type='text/css' href='normalize.css' />

    <style>

        label, input {
            display: inline;
        }

    </style>

    </thead>
    <body>

    <form action="../capture.php" method="POST" target="_blank">
        <div style="margin: 4%; text-align: right; width: 600px;">

            <label for="campo1"> RFID </label>
            <input id="campo1" name="RFID" value="FFFF0001" type="text">
            <br>

            <label for="campo2"> TYPE </label>
            <input id="campo2" name="TYPE" value="1" type="text">
            <br>

            <label for="campo3"> OUTLET  </label>
            <input id="campo3" name="OUTLET" value="4" type="text">
            <br>

            <label for="campo4"> MV </label>
            <input id="campo4" name="MV" value="4" type="text">
            <br>

            <label for="campo5"> MV2 </label>
            <input id="campo5" name="MV2" value="4" type="text">
            <br>

            <label for="campo6"> DURATION </label>
            <input id="campo6" name="DURATION" value="40" type="text">
            <br>

            <label for="campo7"> OFFSET </label>
            <input id="campo7" name="OFFSET" value="2096" type="text">
            <br>

            <label for="campo8"> GAIN </label>
            <input id="campo8" name="GAIN" value="1043" type="text">
            <br>

            <label for="campo9"> RMS </label>
            <input id="campo9" name="RMS" value="4" type="text">
            <br>

            <label for="campo10"> Underflow (UNDER) </label>
            <input id="campo10" name="UNDER" value="1" type="text">
            <br>

            <label for="campo11"> Overflow (OVER) </label>
            <input id="campo11" name="OVER" value="1" type="text">
            <br>

            <label for="campo12"> SIN </label>
            <input id="campo12" name="SIN" value="0;1;2;3;4;5;6;7;8;9;10;11" type="text">
            <br>

            <label for="campo13"> COS </label>
            <input id="campo13" name="COS" value="0;1;2;3;4;5;6;7;8;9;10;11" type="text">
            <br>

            <input type="submit" value="Enviar">

        </div>
    </form>



    </body>
</html>