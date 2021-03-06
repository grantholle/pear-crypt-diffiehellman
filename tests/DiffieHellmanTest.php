<?php

use Pear\Crypt\DiffieHellman;

/**
 * Note: If this test utilises BCMath it will take anything up 2 or 3 minutes to complete. GMP
 * is far faster if enabled for your platform.
 */

class DiffieHellmanTest extends \PHPUnit\Framework\TestCase
{

    public function testDiffieWithSpec()
    {
        $aliceOptions = array(
            'prime'=>'563',
            'generator'=>'5',
            'private'=>'9'
        );
        $bobOptions = array(
            'prime'=>'563',
            'generator'=>'5',
            'private'=>'14'
        );
        $alice = new DiffieHellman($aliceOptions['prime'], $aliceOptions['generator'], $aliceOptions['private']);
        $bob = new DiffieHellman($bobOptions['prime'], $bobOptions['generator'], $bobOptions['private']);
        $alice->generateKeys();
        $bob->generateKeys();

        $this->assertEquals('78', $alice->getPublicKey());
        $this->assertEquals('534', $bob->getPublicKey());

        $aliceSecretKey = $alice->computeSecretKey($bob->getPublicKey())->getSharedSecretKey();
        $bobSecretKey = $bob->computeSecretKey($alice->getPublicKey())->getSharedSecretKey();

        // both Alice and Bob should now have the same secret key
        $this->assertEquals('117', $aliceSecretKey);
        $this->assertEquals('117', $bobSecretKey);
    }

    public function testDiffieWithBinaryFormsAndLargeIntegers()
    {
        $aliceOptions = array(
            'prime' => '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443',
            'generator'=>'2',
            'private' => '99209314066572595236408569591967988557141249561494267486251808035535396332278620143536317681312712891672623072630995180324388841681491857745515696789091127409515009250358965816666146342049838178521379132153348139908016819196219448310107072632515749339055798122538615135104828702523796951800575031871051678091'
        );
        $bobOptions = array(
            'prime' => '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443',
            'generator'=>'2',
            'private' => '33411735792639558625733635717892563612548180650402161151077478314841463707948899786103588912325634730410551946772752880177868972816963551821740386700076034213408153924692562543117963464733156600545484510833072427003474207064650714831083304497737160382097083356876078146231616972608703322302585471319261275664'
        );
        $alice = new DiffieHellman($aliceOptions['prime'], $aliceOptions['generator'], $aliceOptions['private']);
        $bob = new DiffieHellman($bobOptions['prime'], $bobOptions['generator'], $bobOptions['private']);
        $alice->generateKeys();
        $bob->generateKeys();
        $this->assertEquals('0DmJUe9dr02pAtVoGyLHdC+rfBU3mDCelKGPXRDFHofx6mFfN2gcZCmp/ab4ezDXfpIBOatpVdbn2fTNUGo64DtKE2WGTsZCl90RgrGUv8XW/4WDPXeE7g5u7KWHBG/LCE5+XsilE5P5/GIyqr9gsiudTmk+H/hiYZl9Smar9k0=', base64_encode($alice->getPublicKey(DiffieHellman::BINARY)));
        $this->assertEquals('v8puCBaHdch0stxmkyS/sZvZHyB5f0AVkopAQ5wKSZIyEHHcGn7DXXH2u4WdCL+kMr8BcRpxRBJ0TDwfZPpu53nFNEjUd81WlfaKk95e4a/DC4dhlfBkQMebleobhedQPFAo7F9SkHN7uTLa/glxG+3T9DTb+ikcOVPoH3A1G6g=', base64_encode($bob->getPublicKey(DiffieHellman::BINARY)));

        $aliceSecretKey = $alice->computeSecretKey($bob->getPublicKey(DiffieHellman::BINARY), DiffieHellman::BINARY)
            ->getSharedSecretKey(DiffieHellman::BINARY);
        $bobSecretKey = $bob->computeSecretKey($alice->getPublicKey(DiffieHellman::BINARY), DiffieHellman::BINARY)
            ->getSharedSecretKey(DiffieHellman::BINARY);

        // both Alice and Bob should now have the same secret key
        $expectedSharedSecret = base64_decode('FAAkw7NN1+raX9K1+dR3nqX2LZcDYYuZH13lpasaDIM4/ZXqbzdgiHZ86SILN27BjmJObtNQG/SNHfhxMalLMtLv+v0JFte/6+pIvMG9tAoPFsVh2BAvBuNpLY5W5gusgQ2p4pvJK0wz9YJ8iFdOHEOnhzYuN7LS/YXx2rBOz0Q=');
        $this->assertEquals($expectedSharedSecret, $aliceSecretKey);
        $this->assertEquals($expectedSharedSecret, $bobSecretKey);
    }

}
