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

$vApvhdr = "SELECT a.refno, a.valuedate as date,
DATE_ADD(a.valuedate, INTERVAL a.terms DAY) AS due,
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
b.refno, b.payee, b.posted, b.date as cvhdrdate, b.id as cvhdrid,
c.descriptor as supplier, c.code as suppliercode, c.id as supplierid,
d.descriptor as bank, d.code as bankcode, d.acctno, d.id as bankid
FROM cvchkdtl a
LEFT JOIN cvhdr b
ON a.cvhdrid = b.id
LEFT JOIN supplier c
ON b.supplierid = c.id
LEFT JOIN bank d
ON d.id = a.bankacctid";


?>