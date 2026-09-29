import com.jme3.app.SimpleApplication;
import com.jme3.input.KeyInput;
import com.jme3.input.MouseInput;
import com.jme3.input.controls.ActionListener;
import com.jme3.input.controls.KeyTrigger;
import com.jme3.input.controls.MouseButtonTrigger;
import com.jme3.material.Material;
import com.jme3.math.ColorRGBA;
import com.jme3.math.Vector3f;
import com.jme3.scene.Geometry;
import com.jme3.scene.Node;
import com.jme3.scene.shape.Box;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.util.HashMap;
import java.util.Map;

public class ClientGame extends SimpleApplication {

    // =====================================================
    // SERVER
    // =====================================================

    private static final String SERVER_HOST =
            "localhost";

    private static final int SERVER_PORT =
            5000;

    private Socket socket;

    private BufferedReader input;

    private PrintWriter output;

    private int playerId = -1;

    // =====================================================
    // GAME
    // =====================================================

    private final Vector3f playerPosition =
            new Vector3f(0, 1, 0);

    private float speed = 6f;

    private Geometry localPlayer;

    private Node remotePlayers;

    private final Map<Integer, Geometry>
            remotePlayerObjects =
            new HashMap<>();

    // =====================================================
    // INPUT
    // =====================================================

    private boolean forward;
    private boolean backward;
    private boolean left;
    private boolean right;

    // =====================================================
    // MAIN
    // =====================================================

    public static void main(String[] args) {

        ClientGame game =
                new ClientGame();

        game.start();
    }

    // =====================================================
    // INITIALIZE
    // =====================================================

    @Override
    public void simpleInitApp() {

        System.out.println(
                "Starting 3D multiplayer client..."
        );

        setDisplayFps(false);
        setDisplayStatView(false);

        createWorld();

        createLocalPlayer();

        remotePlayers =
                new Node("RemotePlayers");

        rootNode.attachChild(
                remotePlayers
        );

        setupInput();

        connectToServer();

        setupCamera();
    }

    // =====================================================
    // WORLD
    // =====================================================

    private void createWorld() {

        // Ground

        Box groundShape =
                new Box(
                        25,
                        0.1f,
                        25
                );

        Geometry ground =
                new Geometry(
                        "Ground",
                        groundShape
                );

        Material groundMaterial =
                new Material(
                        assetManager,
                        "Common/MatDefs/Light/Lighting.j3md"
                );

        groundMaterial.setBoolean(
                "UseMaterialColors",
                true
        );

        groundMaterial.setColor(
                "Diffuse",
                ColorRGBA.DarkGray
        );

        groundMaterial.setColor(
                "Ambient",
                ColorRGBA.DarkGray
        );

        ground.setMaterial(
                groundMaterial
        );

        rootNode.attachChild(
                ground
        );

        // Directional light

        com.jme3.light.DirectionalLight sun =
                new com.jme3.light.DirectionalLight();

        sun.setDirection(
                new Vector3f(
                        -1,
                        -2,
                        -1
                ).normalizeLocal()
        );

        sun.setColor(
                ColorRGBA.White
        );

        rootNode.addLight(sun);

        // Ambient light

        com.jme3.light.AmbientLight ambient =
                new com.jme3.light.AmbientLight();

        ambient.setColor(
                ColorRGBA.White.mult(0.4f)
        );

        rootNode.addLight(
                ambient
        );
    }

    // =====================================================
    // LOCAL PLAYER
    // =====================================================

    private void createLocalPlayer() {

        Box playerShape =
                new Box(
                        0.5f,
                        1f,
                        0.5f
                );

        localPlayer =
                new Geometry(
                        "LocalPlayer",
                        playerShape
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

        localPlayer.setMaterial(
                material
        );

        localPlayer.setLocalTranslation(
                playerPosition
        );

        rootNode.attachChild(
                localPlayer
        );
    }

    // =====================================================
    // REMOTE PLAYER
    // =====================================================

    private Geometry createRemotePlayer() {

        Box shape =
                new Box(
                        0.5f,
                        1f,
                        0.5f
                );

        Geometry geometry =
                new Geometry(
                        "RemotePlayer",
                        shape
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
                ColorRGBA.Red
        );

        material.setColor(
                "Ambient",
                ColorRGBA.Red
        );

        geometry.setMaterial(
                material
        );

        return geometry;
    }

    // =====================================================
    // INPUT
    // =====================================================

    private void setupInput() {

        inputManager.addMapping(
                "Forward",
                new KeyTrigger(
                        KeyInput.KEY_W
                )
        );

        inputManager.addMapping(
                "Backward",
                new KeyTrigger(
                        KeyInput.KEY_S
                )
        );

        inputManager.addMapping(
                "Left",
                new KeyTrigger(
                        KeyInput.KEY_A
                )
        );

        inputManager.addMapping(
                "Right",
                new KeyTrigger(
                        KeyInput.KEY_D
                )
        );

        inputManager.addMapping(
                "Shoot",
                new MouseButtonTrigger(
                        MouseInput.BUTTON_LEFT
                )
        );

        inputManager.addListener(
                actionListener,
                "Forward",
                "Backward",
                "Left",
                "Right",
                "Shoot"
        );
    }

    // =====================================================
    // ACTION LISTENER
    // =====================================================

    private final ActionListener actionListener =
            new ActionListener() {

                @Override
                public void onAction(
                        String name,
                        boolean isPressed,
                        float tpf) {

                    if (name.equals("Forward")) {
                        forward = isPressed;
                    }

                    else if (name.equals("Backward")) {
                        backward = isPressed;
                    }

                    else if (name.equals("Left")) {
                        left = isPressed;
                    }

                    else if (name.equals("Right")) {
                        right = isPressed;
                    }

                    else if (name.equals("Shoot")
                            && isPressed) {

                        shoot();
                    }
                }
            };

    // =====================================================
    // CONNECT
    // =====================================================

    private void connectToServer() {

        try {

            socket =
                    new Socket(
                            SERVER_HOST,
                            SERVER_PORT
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

            networkThread.setDaemon(
                    true
            );

            networkThread.start();

        } catch (IOException e) {

            System.err.println(
                    "Could not connect to server."
            );

            System.err.println(
                    "Make sure Server.java is running."
            );
        }
    }

    // =====================================================
    // NETWORK RECEIVE
    // =====================================================

    private void receiveNetworkData() {

        try {

            String message;

            while (
                    (message =
                            input.readLine()) != null
            ) {

                processMessage(
                        message
                );
            }

        } catch (IOException e) {

            System.out.println(
                    "Disconnected from server."
            );
        }
    }

    // =====================================================
    // PROCESS MESSAGE
    // =====================================================

    private void processMessage(
            String message) {

        String[] parts =
                message.trim()
                        .split("\\s+");

        if (parts.length == 0) {
            return;
        }

        // ---------------------------------
        // WELCOME
        // ---------------------------------

        if (parts[0].equals("WELCOME")) {

            if (parts.length >= 2) {

                try {

                    playerId =
                            Integer.parseInt(
                                    parts[1]
                            );

                    System.out.println(
                            "Your player ID: " +
                            playerId
                    );

                } catch (NumberFormatException ignored) {
                }
            }
        }

        // ---------------------------------
        // EXISTING PLAYER
        // ---------------------------------

        else if (parts[0].equals("PLAYER")) {

            if (parts.length < 5) {
                return;
            }

            try {

                int id =
                        Integer.parseInt(parts[1]);

                if (id == playerId) {
                    return;
                }

                float x =
                        Float.parseFloat(parts[2]);

                float y =
                        Float.parseFloat(parts[3]);

                float z =
                        Float.parseFloat(parts[4]);

                updateRemotePlayer(
                        id,
                        x,
                        y,
                        z
                );

            } catch (NumberFormatException ignored) {
            }
        }

        // ---------------------------------
        // PLAYER JOINED
        // ---------------------------------

        else if (parts[0].equals("JOIN")) {

            if (parts.length >= 2) {

                System.out.println(