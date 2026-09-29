import com.jme3.app.SimpleApplication;
import com.jme3.input.KeyInput;
import com.jme3.input.MouseInput;
import com.jme3.input.controls.KeyTrigger;
import com.jme3.input.controls.MouseButtonTrigger;
import com.jme3.material.Material;
import com.jme3.math.ColorRGBA;
import com.jme3.math.Vector3f;
import com.jme3.scene.Geometry;
import com.jme3.scene.shape.Box;

import java.io.*;
import java.net.Socket;

public class Main extends SimpleApplication {

    // =========================
    // NETWORK
    // =========================

    private Socket socket;
    private BufferedReader input;
    private PrintWriter output;

    private int playerId = -1;

    // =========================
    // PLAYER
    // =========================

    private final Vector3f playerPosition =
            new Vector3f(0, 1, 0);

    private float speed = 5f;

    private Geometry player;

    // =========================
    // MAIN
    // =========================

    public static void main(String[] args) {

        Main game = new Main();

        game.start();
    }

    // =========================
    // START GAME
    // =========================

    @Override
    public void simpleInitApp() {

        System.out.println(
                "Starting 3D multiplayer client..."
        );

        createWorld();

        createPlayer();

        setupControls();

        connectToServer();

        cam.setLocation(
                new Vector3f(0, 5, 10)
        );

        cam.lookAt(
                playerPosition,
                Vector3f.UNIT_Y
        );
    }

    // =========================
    // WORLD
    // =========================

    private void createWorld() {

        Box floorBox =
                new Box(25, 0.1f, 25);

        Geometry floor =
                new Geometry(
                        "Floor",
                        floorBox
                );

        Material floorMaterial =
                new Material(
                        assetManager,
                        "Common/MatDefs/Light/Lighting.j3md"
                );

        floorMaterial.setBoolean(
                "UseMaterialColors",
                true
        );

        floorMaterial.setColor(
                "Diffuse",
                ColorRGBA.DarkGray
        );

        floorMaterial.setColor(
                "Ambient",
                ColorRGBA.DarkGray
        );

        floor.setMaterial(floorMaterial);

        rootNode.attachChild(floor);

        // Lighting

        com.jme3.light.DirectionalLight light =
                new com.jme3.light.DirectionalLight();

        light.setDirection(
                new Vector3f(
                        -1,
                        -2,
                        -1
                ).normalizeLocal()
        );

        light.setColor(ColorRGBA.White);

        rootNode.addLight(light);

        com.jme3.light.AmbientLight ambient =
                new com.jme3.light.AmbientLight();

        ambient.setColor(
                ColorRGBA.White.mult(0.4f)
        );

        rootNode.addLight(ambient);
    }

    // =========================
    // PLAYER
    // =========================

    private void createPlayer() {

        Box box =
                new Box(0.5f, 1f, 0.5f);

        player =
                new Geometry(
                        "Player",
                        box
                );

        Material material =
                new Material(
                        assetManager,
                        "Common/MatDefs/Light/Lighting.j3md"
                );

        material.setBoolean(
                "UseMaterialColors",
                true
        );

        material.setColor(
                "Diffuse",
                ColorRGBA.Blue
        );

        material.setColor(
                "Ambient",
                ColorRGBA.Blue
        );

        player.setMaterial(material);

        player.setLocalTranslation(
                playerPosition
        );

        rootNode.attachChild(player);
    }

    // =========================
    // CONTROLS
    // =========================

    private void setupControls() {

        inputManager.addMapping(
                "Forward",
                new KeyTrigger(KeyInput.KEY_W)
        );

        inputManager.addMapping(
                "Backward",
                new KeyTrigger(KeyInput.KEY_S)
        );

        inputManager.addMapping(
                "Left",
                new KeyTrigger(KeyInput.KEY_A)
        );

        inputManager.addMapping(
                "Right",
                new KeyTrigger(KeyInput.KEY_D)
        );

        inputManager.addMapping(
                "Shoot",
                new MouseButtonTrigger(
                        MouseInput.BUTTON_LEFT
                )
        );
    }

    // =========================
    // SERVER CONNECTION
    // =========================

    private void connectToServer() {

        try {

            socket =
                    new Socket(
                            "localhost",
                            5000
                    );

            input =
                    new BufferedReader(
                            new InputStreamReader(
                                    socket.getInputStream()
                            )
                    );

            output =
                    new PrintWriter(
                            socket.getOutputStream(),
                            true
                    );

            System.out.println(
                    "Connected to server!"
            );

            Thread networkThread =
                    new Thread(
                            this::receiveNetworkData
                    );

            networkThread.setDaemon(true);

            networkThread.start();

        } catch (IOException e) {

            System.out.println(
                    "Could not connect to server."
            );

            e.printStackTrace();
        }
    }

    // =========================
    // RECEIVE NETWORK DATA
    // =========================

    private void receiveNetworkData() {

        try {

            String message;

            while (
                    (message =
                            input.readLine()) != null
            ) {

                System.out.println(
                        "SERVER: " + message
                );

                if (message.startsWith("WELCOME")) {

                    String[] parts =
                            message.split(" ");

                    playerId =
                            Integer.parseInt(
                                    parts[1]
                            );

                    System.out.println(
                            "Your player ID: " +
                            playerId
                    );
                }
            }

        } catch (IOException e) {

            System.out.println(
                    "Disconnected from server."
            );
        }
    }

    // =========================
    // GAME LOOP
    // =========================

    @Override
    public void simpleUpdate(
            float tpf
    ) {

        movePlayer(tpf);

        updateCamera();

        sendPosition();
    }

    // =========================
    // MOVEMENT
    // =========================

    private void movePlayer(
            float tpf
    ) {

        Vector3f movement =
                new Vector3f();

        if (inputManager.isKeyDown(
                KeyInput.KEY_W
        )) {

            movement.z -= 1;
        }

        if (inputManager.isKeyDown(
                KeyInput.KEY_S
        )) {

            movement.z += 1;
        }

        if (inputManager.isKeyDown(
                KeyInput.KEY_A
        )) {

            movement.x -= 1;
        }

        if (inputManager.isKeyDown(
                KeyInput.KEY_D
        )) {

            movement.x += 1;
        }

        if (movement.lengthSquared() > 0) {

            movement.normalizeLocal();

            playerPosition.addLocal(
                    movement.mult(
                            speed * tpf
                    )
            );
        }

        // Arena boundaries

        playerPosition.x =
                Math.max(
                        -24,
                        Math.min(
                                24,
                                playerPosition.x
                        )
                );

        playerPosition.z =
                Math.max(
                        -24,
                        Math.min(
                                24,
                                playerPosition.z
                        )
                );

        player.setLocalTranslation(
                playerPosition
        );
    }

    // =========================
    // CAMERA
    // =========================

    private void updateCamera() {

        Vector3f cameraPosition =
                new Vector3f(
                        playerPosition.x,
                        playerPosition.y + 5,
                        playerPosition.z + 10
                );

        cam.setLocation(
                cameraPosition
        );

        cam.lookAt(
                playerPosition,
                Vector3f.UNIT_Y
        );
    }

    // =========================
    // SEND POSITION
    // =========================

    private void sendPosition() {

        if (output == null)
            return;

        output.println(
                "POS " +
                playerPosition.x +
                " " +
                playerPosition.y +
                " " +
                playerPosition.z
        );
    }

    // =========================
    // SHOOT
    // =========================

    public void shoot() {

        if (output != null) {

            output.println(
                    "SHOOT"
            );
        }
    }

    // =========================
    // CLOSE
    // =========================

    @Override
    public void destroy() {

        try {

            if (socket != null) {
                socket.close();
            }

        } catch (IOException ignored) {
        }

        super.destroy();
    }
}
