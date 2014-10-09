<h2 id="page_title">DotPay</h2>
<!-- data aktualizacji 18-01-2008 -->


<form action="https://ssl.dotpay.pl" method="post">

    <input type=hidden name="id" value="<?php echo $options['id']; ?>">
    <!-- wpisz swój numer id otrzymany od Dotpay.pl -->

    <input type=hidden name="lang" value="<?php echo $options['lang']; ?>">
    <!-- dostępne wersje językowe: pl, en, de -->

    <input type=hidden name="potw" value="1">
    <!-- parametr definiujący przesłanie potwierdzenia zapłaty:
                    0 - brak potwierdzenia, 
                    1 - potwierdzenia zostanie wysłane na adres zdefiniowany w email_potw -->

<!--    <input type=hidden name="email_potw" value="{{ settings:contact_email }}">-->
    <input type=hidden name="email_potw" value="<?php echo $billing->email; ?>">
    <!-- adres email na który zostanie przesłane potwierdzenie zapłaty -->

    <input type="hidden" name="opis" value="{{ settings:site_name }}">
    <!-- domyslny opis transakcji -->

    <input type="hidden" name="kwota" size="15" value="<?php echo $order->total + $order->shipping; ?>">
    <input type=hidden name="waluta" value="PLN">

    <input type=hidden name="URL" value="{{ url:site }}store/payments/success">
    <input type=hidden name="URLC" value="{{ url:site }}store/payments/callback/dotpay/notify/<?php echo $order->id; ?>">
    <input type=hidden name="type" value="0">
    <input type=hidden name="blokuj" value="1">
    <input type=hidden name="buttontext" value="Powrót do serwisu">
    <label>Wybierz metodę płatności:</label>
    <select name="kanal">
        <option value="0">VISA, MasterCard, EuroCard, JCB, Diners Club</option>
        <option value="212">PayPal</option>
        <option value="1">mTransfer (mBank)</option>
        <option value="2">Place z Inteligo  (konto Inteligo)</option>
        <option value="24">mPay - platnosc telefonem komorkowym</option>
        <option value="21">Dotpay Moje Rachunki</option>				
        <option value="3">MultiTransfer (MultiBank)</option>
        <option value="6">Przelew24 BZWBK</option>
        <option value="7">ING Bank Slaski</option>
        <option value="17">Plac z Nordea</option>
        <option value="18">Przelew z BPH</option>
        <option value="22">Kupon Ukash</option>
        <option value="11">Przekaz/Przelew bankowy</option>				
        <option value="8">SEZAM (Bank BPH SA)</option>
        <option value="9">Pekao24 (Bank Pekao S.A.)</option>
        <option value="10">MilleNet (Millennium Bank)</option>				
        <option value="13">Deutsche Bank PBC S.A.</option>
        <option value="14">Kredyt Bank S.A. (KB24)</option>
        <option value="15">Inteligo (Bank PKO BP)</option>
        <option value="16">Lukas Bank</option>
        <option value="19">CitiBank Handlowy</option>
        <option value="25">InvestBank</option>	
        <option value="32">Fortis Bank</option> 
        <option value="26">Bank Ochrony Srodowiska</option>
        <option value="33">Volkswagen Bank Polska</option>
        <option value="31">Zaplac w Zabce</option>	
    </select>
    <p>
        <input type="submit" value="zaplac on-line" name="b1">
    </p>
</form>