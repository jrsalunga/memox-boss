<ul class="nav nav-pills nav-stacked">
    <li <?=($activ=='apvhdr')?'class="active"':''?> >
        <a href="apvhdr">Accounts Payable</a>
    </li>
    <li <?=($activ=='apvhdr-age')?'class="active"':''?> >
        <a href="apvhdr-age">Accounts Payable (Aged)</a>
    </li>
    <li <?=($activ=='apvhdr-account')?'class="active"':''?> >
        <a href="apvhdr-account">AP (Accounts)</a>
    </li>
    <li <?=($activ=='cvhdr-supplier')?'class="active"':''?> >
        <a href="cvhdr-supplier">Check Voucher</a>
    </li>
    <li <?=($activ=='cvhdr')?'class="active"':''?> >
        <a href="cvhdr">CV Schedule</a>
    <li>
    <li <?=($activ=='cv-sched')?'class="active"':''?> >
        <a href="cv-sched">CV Schedule (Bank)</a>
    <li>
</ul>