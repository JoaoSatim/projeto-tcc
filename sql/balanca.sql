CREATE TABLE balanca_entrada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100),
    placa VARCHAR(20),
    motorista VARCHAR(100),
    peso_entrada INT,
    data_entrada DATETIME
);

CREATE TABLE balanca_saida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(20),
    peso_saida INT,
    data_saida DATETIME
);
