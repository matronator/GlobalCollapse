UPDATE `drugs` SET
`id` = '2',
`name` = 'Ecstasy',
`price` = '8',
`min` = '8',
`max` = '20',
`past_price` = '6',
`supply_max` = '100000',
`supply` = '100000',
`updated` = now()
WHERE `id` = '2';

UPDATE `drugs` SET
`id` = '1',
`name` = 'Weed',
`price` = '7',
`min` = '3',
`max` = '15',
`past_price` = '3',
`supply_max` = NULL,
`supply` = NULL,
`updated` = now()
WHERE `id` = '1';
