function rupiah(n){
  const formatter = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    });
  const formatted = formatter.format(n);
  return formatted;
}

function dollarSG(n){
  const formatter = new Intl.NumberFormat('sg-SG', {
      style: 'currency',
      currency: 'SGD'
    });
  const formatted = formatter.format(n);
  return formatted;
}