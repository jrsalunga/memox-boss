<?php



$vProject = "SELECT p.id, p.code, p.descriptor, c.code as customer, p.customerid, s.code as salesman, p.salesmanid,
        p.location, p.typeid, t.descriptor as type, p.datestart, p.dateend, p.dateendx, p.amount, p.balance, p.notes
FROM project p
LEFT JOIN customer c ON p.customerid=c.id
LEFT JOIN salesman s ON p.salesmanid = s.id
LEFT JOIN project_type t ON p.type = t.code";

$vMaterial = "SELECT a.code, a.descriptor, a.typeid, c.descriptor as type, a.matcatid, b.descriptor as matcat, a.uom, a.longdesc, a.onhand, a.minlevel, a.maxlevel, a.reorderqty, a.avecost, a.id
FROM material a, matcat b, material_type c
WHERE a.matcatid = b.id AND a.typeid = c.code";

$vApvhdr = "SELECT a.refno, a.date,
DATE_ADD(a.date, INTERVAL a.terms DAY) AS due,
b.descriptor as supplier, a.supprefno, a.porefno, a.terms, a.totamount, a.balance, a.posted, a.notes, a.supplierid, b.code as suppliercode, a.cancelled, a.printctr, a.totline, a.id
FROM apvhdr a
LEFT JOIN supplier b
ON a.supplierid = b.id";


$vApvdtl = "SELECT a.apvhdrid, a.accountid, a.type, a.projectid, a.amount, a.id,
d.refno, b.descriptor as account, b.code as account_code, c.descriptor as project, d.date
FROM apvdtl a
LEFT JOIN account b
ON a.accountid = b.id
INNER JOIN project c
ON a.projectid = c.id
LEFT JOIN apvhdr d
ON a.apvhdrid = d.id
ORDER BY d.date DESC";

$vCvhdr = "SELECT a.refno, a.date, b.descriptor as supplier, a.payee, a.totapvamt, a.totchkamt, a.posted, a.supplierid, a.notes, a.cancelled, a.totapvline, a.totchkline, a.id
FROM cvhdr a
LEFT JOIN supplier b
ON a.supplierid = b.id";

$vCvchkdtl = "SELECT a.checkno, a.checkdate, a.amount, a.id,
b.refno, b.payee, b.posted, b.cancelled, b.date as cvhdrdate, b.id as cvhdrid,
c.descriptor as supplier, c.code as suppliercode, c.id as supplierid,
d.descriptor as bank, d.code as bankcode, d.acctno, d.id as bankid
FROM cvchkdtl a
LEFT JOIN cvhdr b
ON a.cvhdrid = b.id
LEFT JOIN supplier c
ON b.supplierid = c.id
LEFT JOIN bank d
ON d.id = a.bankacctid";

$vCvapvdtl = "SELECT a.amount, a.id, a.apvhdrid, a.cvhdrid,
b.refno, b.date, b.due, b.supplier, b.supplierid, b.supprefno, b.porefno, b.terms, b.totamount, b.notes, b.posted, b.cancelled
FROM cvapvdtl a
LEFT JOIN vapvhdr b
ON a.apvhdrid = b.id
ORDER BY b.due DESC";



$vxCvchkdtl = "SELECT a.checkno, a.checkdate, a.amount, a.id,
b.refno AS cvrefno, b.date AS cvdate, b.payee AS cvpayee, b.notes AS cvnotes, b.posted AS cvposted, b.cancelled AS cvcancelled, b.id AS cvhdrid,
c.amount AS cvapvdtlamt, c.id as cvapvdtlid,
d.refno AS aprefno, d.date AS apdate, DATE_ADD(d.date, INTERVAL d.terms DAY) AS apdue, d.porefno AS apporefno, d.terms AS apterms, d.totamount AS aptotamount, d.balance AS apbalance, d.notes AS apnotes, d.posted AS apposted, d.cancelled AS apcancelled, d.id AS apvhdrid,
e.amount AS apvdtlamt, e.id AS apvdtlid,
f.code AS accountcode, f.descriptor AS account, f.id AS accountid,
g.code AS acctcatcode, g.descriptor AS acctcat, g.id AS acctcatid,
h.code AS bankcode, h.descriptor AS bank, h.acctno AS bankacctno, h.id AS bankid,
i.code AS suppliercode, i.descriptor AS supplier, i.terms AS supplierterms, i.balance AS supplierbalance, i.id AS supplierid
FROM cvchkdtl a
LEFT JOIN cvhdr b
ON b.id = a.cvhdrid
LEFT JOIN cvapvdtl c
ON c.cvhdrid = b.id
LEFT JOIN apvhdr d
ON d.id = c.apvhdrid
LEFT JOIN apvdtl e
ON e.apvhdrid = d.id
LEFT JOIN account f
ON f.id = e.accountid
LEFT JOIN acctcat g
ON g.id = f.acctcatid
LEFT JOIN bank h
ON h.id = a.bankacctid
LEFT JOIN supplier i
ON i.id = d.supplierid
GROUP BY a.id
ORDER BY a.checkdate DESC";

$vxCvhdr = "SELECT
a.refno AS cvrefno, a.date AS cvdate, a.payee AS cvpayee, a.totapvamt AS cvtotapvamt, a.totchkamt AS cvtotchkamt, a.notes AS cvnotes, a.posted AS cvposted, a.cancelled AS cvcancelled, a.id AS cvhdrid,
b.amount AS cvapvdtlamt, b.id AS cvapvdtlid,
c.refno AS aprefno, c.date AS apdate, c.due AS apdue, c.supplier, c.supplierid, c.porefno AS apporefno, c.terms AS apterms, c.totamount AS aptotamount, c.balance AS apbalance, c.notes AS apnotes, c.posted AS apposted, c.cancelled AS apcancelled, c.id AS apvhdrid,
d.amount AS apvdtlamt, d.id AS apvdtlid,
e.code AS accountcode, e.descriptor AS account, e.id AS accountid,
f.code AS acctcatcode, f.descriptor AS acctcat, f.id AS acctcatid
FROM cvhdr a
LEFT JOIN cvapvdtl b
ON b.cvhdrid = a.id
LEFT JOIN vapvhdr c
ON c.id = b.apvhdrid
LEFT JOIN apvdtl d
ON d.apvhdrid = c.id
LEFT JOIN account e
ON e.id = d.accountid
LEFT JOIN acctcat f
ON f.id = e.acctcatid
GROUP BY a.id
ORDER BY a.date DESC";


$vcCvchdtl = "SELECT a.checkno, a.checkdate, a.amount, a.id,
b.id AS cvhdrid, c.id as cvapvdtlid, d.id AS apvhdrid, e.id AS apvdtlid, f.id AS accountid, g.id AS acctcatid, h.id AS bankid, i.id AS supplierid
FROM cvchkdtl a
LEFT JOIN cvhdr b
ON b.id = a.cvhdrid
LEFT JOIN cvapvdtl c
ON c.cvhdrid = b.id
LEFT JOIN apvhdr d
ON d.id = c.apvhdrid
LEFT JOIN apvdtl e
ON e.apvhdrid = d.id
LEFT JOIN account f
ON f.id = e.accountid
LEFT JOIN acctcat g
ON g.id = f.acctcatid
LEFT JOIN bank h
ON h.id = a.bankacctid
LEFT JOIN supplier i
ON i.id = d.supplierid
GROUP BY a.id
ORDER BY a.checkdate DESC";



$product2 = "DROP VIEW IF EXISTS `memox`.`vproduct2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW  `memox`.`vproduct2` AS select `a`.`code` AS `code`,`c`.`descriptor` AS `brand`,`d`.`descriptor` AS `model`,`a`.`descriptor` AS `descriptor`,`e`.`descriptor` AS `category`,`b`.`descriptor` AS `type`,`a`.`onhand` AS `onhand`,`a`.`minlevel` AS `minlevel`,`a`.`maxlevel` AS `maxlevel`,`a`.`reorderqty` AS `reorderqty`,`a`.`unitprice` AS `unitprice`,`a`.`floorprice` AS `floorprice`,`a`.`avecost` AS `avecost`,`a`.`brandid` AS `brandid`,`a`.`modelid` AS `modelid`,`a`.`prodcatid` AS `prodcatid`,`a`.`typeid` AS `typeid`,`a`.`serialized` AS `serialized`,`a`.`uom` AS `uom`,`a`.`longdesc` AS `longdesc`,`a`.`picfile` AS `picfile`,`a`.`id` AS `id` from ((((`product` `a` join `product_type` `b`) join `brand` `c`) join `model` `d`) join `prodcat` `e`) where ((`a`.`typeid` = `b`.`code`) and (`a`.`brandid` = `c`.`id`) and (`a`.`modelid` = `d`.`id`) and (`a`.`prodcatid` = `e`.`id`)) order by `a`.`descriptor`;";