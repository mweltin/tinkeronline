select * from 

select * from account 
left join registrar on registrar.registrar_id = account.registrar_id
where registrar.account_id = 90
and account.account_id != 90;