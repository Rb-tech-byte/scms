--update user
DELIMITER $$

CREATE TRIGGER AfterUpdateUser
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.updated_at <> OLD.updated_at THEN
        INSERT INTO user_audit_logs(user_id, action_type, detail, action_time)
        VALUES(OLD.id, 'update', CONCAT('User updated: ', OLD.name), NOW());
    END IF;
END$$

DELIMITER ;

--delete user
DELIMITER $$

CREATE TRIGGER BeforeDeleteUser
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_audit_logs(user_id, action_type, detail, action_time)
    VALUES(OLD.id, 'delete', CONCAT('User deleted: ', OLD.name), NOW());
END$$

DELIMITER ;


--update payment
DELIMITER $$

CREATE TRIGGER AfterUpdatePayment
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    IF NEW.amount <> OLD.amount OR NEW.payment_status <> OLD.payment_status THEN
        INSERT INTO payment_audit_logs(payment_id, action_type, detail, action_time)
        VALUES(OLD.id, 'update', CONCAT('Payment updated. Amount: ', OLD.amount, ' to ', NEW.amount, ', Status: ', OLD.payment_status, ' to ', NEW.payment_status), NOW());
    END IF;
END$$

DELIMITER ;


--delete payment
DELIMITER $$

CREATE TRIGGER BeforeDeletePayment
BEFORE DELETE ON payments
FOR EACH ROW
BEGIN
    INSERT INTO payment_audit_logs(payment_id, action_type, detail, action_time)
    VALUES(OLD.id, 'delete', CONCAT('Payment deleted. Amount: ', OLD.amount, ', Status: ', OLD.payment_status), NOW());
END$$

DELIMITER ;

--invoice update
DELIMITER $$

CREATE TRIGGER AfterUpdateInvoice
AFTER UPDATE ON invoices
FOR EACH ROW
BEGIN
    IF NEW.balance <> OLD.balance OR NEW.paid_amount <> OLD.paid_amount OR NEW.status <> OLD.status THEN
        INSERT INTO invoice_audit_logs(invoice_id, action_type, detail, action_time)
        VALUES(OLD.id, 'update', CONCAT('Invoice updated. Balance changed from ', OLD.balance, ' to ', NEW.balance, ', Paid amount changed from ', OLD.paid_amount, ' to ', NEW.paid_amount, ', Status changed from ', OLD.status, ' to ', NEW.status), NOW());
    END IF;
END$$

DELIMITER ;


--invoice deletion
DELIMITER $$

CREATE TRIGGER BeforeDeleteInvoice
BEFORE DELETE ON invoices
FOR EACH ROW
BEGIN
    INSERT INTO invoice_audit_logs(invoice_id, action_type, detail, action_time)
    VALUES(OLD.id, 'delete', CONCAT('Invoice deleted. Final Balance: ', OLD.balance, ', Paid amount: ', OLD.paid_amount), NOW());
END$$

DELIMITER ;



